<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers;

use Dracodeum\Kit\{
	Manager,
	Traits
};
use Dracodeum\Kit\Interfaces\{
	DebugInfo as IDebugInfo,
	Keyable as IKeyable
};
use Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Managers\Properties\{
	Property,
	Exceptions
};
use Dracodeum\Kit\Root\System;
use Dracodeum\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Dracodeum\Kit\Interfaces\Propertiesable as IPropertiesable;
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * This manager handles and stores a separate set of properties for an object, which may be lazy-loaded and restricted 
 * to a specific mode of operation (strict read-only, read-only, read-write, write-only, write-once and 
 * transient write-once).
 * 
 * Each individual property may be set with restrictions and bindings, such as being set as required, 
 * restricted to a specific mode of operation, bound to existing object properties, have a default value, 
 * have their own accessors (a getter and a setter) and their own type or evaluator to limit the type of values 
 * each one may hold.
 * 
 * These properties may also be persisted to any data storage of choice, through the addition and implementation of 
 * persistence functions to both insert and update property values, with each individual property able to be defined as 
 * automatic (automatically generated value during insert, disallowing the value to be set before insertion) or 
 * immutable (disallowing the value to be set to a new value after insertion) or both.
 */
class Properties extends Manager implements IDebugInfo, IDebugInfoProcessor, IKeyable
{
	//Traits
	use Traits\DebugInfo;
	
	
	
	//Public constants
	/** Allowed modes. */
	public const MODES = ['r', 'r+', 'rw', 'w', 'w-', 'w--'];
	
	
	
	//Private constants
	/** Lazy flag. */
	private const FLAG_LAZY = 0x01;
	
	/** Initialized flag. */
	private const FLAG_INITIALIZED = 0x02;
	
	/** Initializing flag. */
	private const FLAG_INITIALIZING = 0x04;
	
	/** Read-only flag. */
	private const FLAG_READONLY = 0x08;
	
	/** Persisted flag. */
	private const FLAG_PERSISTED = 0x10;
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var string */
	private $mode = 'rw';
	
	/** @var int */
	private $flags = 0x00;
	
	/** @var bool[] */
	private $required_map = [];
	
	/** @var \Closure|null */
	private $builder = null;
	
	/** @var \Closure|null */
	private $remainderer = null;
	
	/** @var \Dracodeum\Kit\Managers\Properties\Property[] */
	private $properties = [];
	
	/** @var \Dracodeum\Kit\Interfaces\Propertiesable|null */
	private $fallback_object = null;
	
	/** @var array */
	private $persisted_values = [];
	
	/** @var string[] */
	private $persisted_keys = [];
	
	/** @var \Closure[] */
	private $pre_persistent_changes_callbacks = [];
	
	/** @var \Closure[] */
	private $post_persistent_changes_callbacks = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param object $owner
	 * <p>The owner object to instantiate with.</p>
	 * @param bool $lazy [default = false] 
	 * <p>Use lazy-loading, so that each property is only loaded on access.<br>
	 * <br>
	 * NOTE: With lazy-loading, the existence of each property becomes unknown ahead of time, 
	 * therefore when retrieving all properties, only the currently loaded ones are returned.</p>
	 * @param string $mode [default = 'rw']
	 * <p>The base mode to set for all properties, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow all properties to be only strictly read from, 
	 * so that they cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow all properties to be only read from (read-only), 
	 * although they may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow all properties to be both read from 
	 * and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow all properties to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow all properties to be only written to, 
	 * but only once during initialization (write-once).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w--</samp> : Allow all properties to be only written to, 
	 * but only once during initialization (write-once), and drop them immediately after initialization (transient).<br>
	 * <br>
	 * All properties default to the mode defined here, but if another mode is set, it becomes restricted as so:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp>, <samp>w-</samp> or <samp>w--</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp>, <samp>w-</samp> and <samp>w--</samp> are allowed.</p>
	 */
	final public function __construct(object $owner, bool $lazy = false, string $mode = 'rw')
	{
		//guard
		UCall::guardParameter('mode', $mode, in_array($mode, self::MODES, true), [
			'hint_message' => "Only the following mode is allowed in manager with owner {{owner}}: {{modes}}.",
			'hint_message_plural' => "Only the following modes are allowed in manager with owner {{owner}}: {{modes}}.",
			'hint_message_number' => count(self::MODES),
			'parameters' => ['owner' => $owner, 'modes' => self::MODES],
			'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
		]);
		
		//initialize
		$this->owner = $owner;
		$this->mode = $mode;
		
		//lazy
		if ($lazy) {
			$this->flags |= self::FLAG_LAZY;
		}
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		//properties
		$properties = [];
		foreach ($this->properties as $name => $property) {
			$properties[$name] = $property->isInitialized() ? $property->getValue(true) : null;
		}
		
		//set
		if (System::getDumpVerbosityLevel() >= EDumpVerbosityLevel::MEDIUM) {
			foreach ($properties as $name => $value) {
				//initialize
				$property = $this->getProperty($name);
				$property_mode = $property->getMode();
				$property_debug_name = "{$property_mode}:{$name}";
				
				//lazy
				if ($property->isLazy()) {
					$property_debug_name = "lazy {$property_debug_name}";
				}
				
				//persistence
				if ($property->isAutoImmutable()) {
					$property_debug_name = "auto-immutable {$property_debug_name}";
				} elseif ($property->isImmutable()) {
					$property_debug_name = "immutable {$property_debug_name}";
				} elseif ($property->isAutomatic()) {
					$property_debug_name = "automatic {$property_debug_name}";
				}
				
				//read-only
				if ($this->isReadonly()) {
					$property_debug_name_prefix = $property_mode[0] === 'r' ? '(readonly)' : '(locked)';
					$property_debug_name = "{$property_debug_name_prefix} {$property_debug_name}";
				}
				
				//uninitialized
				if (!$property->isInitialized()) {
					$property_debug_name = "(uninitialized) {$property_debug_name}";
				}
				
				//set
				$info->set($property_debug_name, $value);
			}
		} else {
			$info->setAll($properties);
		}
	}
	
	
	
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Keyable)
	/** {@inheritdoc} */
	final public function toKey(bool $recursive = false, ?bool &$safe = null): string
	{
		$properties = [];
		foreach ($this->properties as $name => $property) {
			if ($property->isInitialized()) {
				$properties[$name] = $property->getValue(true);
			}
		}
		return get_class($this->owner) . '@properties:' . UType::keyValue($properties, $recursive, false, $safe);
	}
	
	
	
	//Public methods
	/**
	 * Create a property instance with a given name.
	 * 
	 * @param string $name
	 * <p>The name to create with.</p>
	 * @return \Dracodeum\Kit\Managers\Properties\Property
	 * <p>The created property instance with the given name.</p>
	 */
	public function createProperty(string $name): Property
	{
		return new Property($this, $name);
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @return object
	 * <p>The owner object.</p>
	 */
	final public function getOwner(): object
	{
		return $this->owner;
	}
	
	/**
	 * Check if lazy-loading is enabled.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if lazy-loading is enabled.</p>
	 */
	final public function isLazy(): bool
	{
		return $this->flags & self::FLAG_LAZY;
	}
	
	/**
	 * Get mode.
	 * 
	 * @return string
	 * <p>The mode.</p>
	 */
	final public function getMode(): string
	{
		return $this->mode;
	}
	
	/**
	 * Check if is read-only.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	final public function isReadonly(): bool
	{
		return $this->flags & self::FLAG_READONLY;
	}
	
	/**
	 * Set as read-only.
	 * 
	 * This method may only be called after initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly(): Properties
	{
		//guard
		UCall::guard($this->isInitialized(), [
			'hint_message' => "This method may only be called after initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		
		//set
		$this->flags |= self::FLAG_READONLY;
		
		//return
		return $this;
	}
	
	/**
	 * Add required property name.
	 * 
	 * The property, corresponding to the given name added here, must be given during initialization.<br>
	 * <br>
	 * This method may only be called before initialization, with lazy-loading enabled 
	 * and only if the base mode is not set to strict read-only.
	 * 
	 * @param string $name
	 * <p>The name to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyName(string $name): Properties
	{
		return $this->addRequiredPropertyNames([$name]);
	}
	
	/**
	 * Add required property names.
	 * 
	 * The properties, corresponding to the given names added here, must be given during initialization.<br>
	 * <br>
	 * This method may only be called before initialization, with lazy-loading enabled 
	 * and only if the base mode is not set to strict read-only.
	 * 
	 * @param string[] $names
	 * <p>The names to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names): Properties
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard($this->isLazy(), [
			'hint_message' => "In order to explicitly set property {{names}} as required " . 
				"in manager with owner {{owner}}, please use the {{method}} method instead from " . 
				"the corresponding property instance, as lazy-loading is disabled in this manager.",
			'hint_message_plural' => "In order to explicitly set properties {{names}} as required " . 
				"in manager with owner {{owner}}, please use the {{method}} method instead from " . 
				"the corresponding property instances, as lazy-loading is disabled in this manager.",
			'hint_message_number' => count($names),
			'parameters' => ['names' => $names, 'owner' => $this->owner, 'method' => 'setAsRequired'],
			'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
		]);
		UCall::guard($this->mode !== 'r', [
			'error_message' => "Required property names cannot be set as all properties are strictly read-only, " . 
				"in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		
		//add
		$this->required_map += array_fill_keys($names, true);
		
		//return
		return $this;
	}
	
	/**
	 * Check if a given property name is required.
	 * 
	 * @param string $name
	 * <p>The name to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given property name is required.</p>
	 */
	final public function isRequiredPropertyName(string $name): bool
	{
		return $this->isLazy() ? isset($this->required_map[$name]) : $this->getProperty($name)->isRequired();
	}
	
	/**
	 * Add a new property with a given name.
	 * 
	 * This method may only be called before initialization and with lazy-loading disabled.
	 * 
	 * @param string $name
	 * <p>The name to add with.</p>
	 * @return \Dracodeum\Kit\Managers\Properties\Property
	 * <p>The newly added property instance with the given name.</p>
	 */
	final public function addProperty(string $name): Property
	{
		//guard
		UCall::guard(!$this->isLazy(), [
			'hint_message' => "In order to add new properties to manager with owner {{owner}}, " . 
				"please set and use a builder function instead, as lazy-loading is enabled in this manager.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guardParameter('name', $name, !isset($this->properties[$name]), [
			'error_message' => "Property {{name}} already added to manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//property
		$property = $this->createProperty($name);
		UCall::guardInternal($property->getName() === $name, [
			'error_message' => "Property name {{property.getName()}} mismatches the expected name {{name}}, " . 
				"in manager with owner {{owner}}.",
			'parameters' => ['property' => $property, 'name' => $name, 'owner' => $this->owner]
		]);
		UCall::guardInternal($property->getManager() === $this, [
			'error_message' => "Manager mismatch for property {{property.getName()}}, in manager with owner {{owner}}.",
			'hint_message' => "The manager which a given property is set with and the one it is being added to " . 
				"must be exactly the same.",
			'parameters' => ['property' => $property, 'owner' => $this->owner]
		]);
		$this->properties[$name] = $property;
		
		//return
		return $property;
	}
	
	/**
	 * Set builder function.
	 * 
	 * A builder function is required to be set when lazy-loading is enabled.<br>
	 * <br>
	 * This method may only be called before initialization and with lazy-loading enabled.
	 * 
	 * @param callable $builder
	 * <p>The function to set to build a property instance with a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name): ?Dracodeum\Kit\Managers\Properties\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build with.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Managers\Properties\Property|null</b></code><br>
	 * The built property instance with the given name or <code>null</code> if none was built.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder(callable $builder): Properties
	{
		UCall::guard($this->isLazy(), [
			'hint_message' => "A builder function cannot be set in manager with owner {{owner}}, " . 
				"as lazy-loading is disabled.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::assert('builder', $builder, function (string $name): ?Property {});
		$this->builder = \Closure::fromCallable($builder);
		return $this;
	}
	
	/**
	 * Check if has already been persisted at least once.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Check if has already been recursively persisted at least once.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has already been persisted at least once.</p>
	 */
	final public function isPersisted(bool $recursive = false): bool
	{
		if ($this->flags & self::FLAG_PERSISTED) {
			if ($recursive) {
				foreach ($this->properties as $property) {
					if (
						$property->isInitialized() && !$property->hasLazyValue() && 
						!UType::persistedValue($property->getValue(true), true, true)
					) {
						return false;
					}
				}
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Set remainderer function.
	 * 
	 * A remainderer function is meant to handle any remaining properties which were not found during initialization, 
	 * and is executed before any given properties are set.<br>
	 * It is also executed even if all given properties were found without any remaining properties left to handle, 
	 * resulting in an execution with an empty properties array.<br>
	 * <br>
	 * This method may only be called before initialization.
	 * 
	 * @param callable $remainderer
	 * <p>The function to set to handle a given set of remaining properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $properties): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The remaining properties to handle, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Required properties may also be given as an array of values 
	 * (<samp>[value1, value2, ...]</samp>), in the same order as how these properties were first declared.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setRemainderer(callable $remainderer): Properties
	{
		UCall::guard(!$this->isInitialized(), [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::assert('remainderer', $remainderer, function (array $properties): void {});
		$this->remainderer = \Closure::fromCallable($remainderer);
		return $this;
	}
	
	/**
	 * Set fallback object.
	 * 
	 * By setting a fallback object, any property not found in this manager is attempted to be got from 
	 * the given fallback object instead.
	 * 
	 * @param \Dracodeum\Kit\Interfaces\Propertiesable $object
	 * <p>The object to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setFallbackObject(IPropertiesable $object): Properties
	{
		$this->fallback_object = $object;
		return $this;
	}
	
	/**
	 * Unset fallback object.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetFallbackObject(): Properties
	{
		$this->fallback_object = null;
		return $this;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized(): bool
	{
		return $this->flags & self::FLAG_INITIALIZED;
	}
	
	/**
	 * Check if is initializing.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is initializing.</p>
	 */
	final public function isInitializing(): bool
	{
		return $this->flags & self::FLAG_INITIALIZING;
	}
	
	/**
	 * Initialize.
	 * 
	 * @param array $properties [default = []]
	 * <p>The properties to initialize with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param bool $persisted [default = false]
	 * <p>Set properties as having already been persisted at least once.</p>
	 * @param array|null $remainder [reference output] [default = null]
	 * <p>The properties remainder, which, if set, is gracefully filled with all remaining properties which have 
	 * not been found from the given <var>$properties</var> above, as <samp>name => value</samp> pairs or 
	 * an array of required property values or both.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(
		array $properties = [], bool $persisted = false, ?array &$remainder = null
	): Properties
	{
		//guard
		UCall::guard(!$this->isInitialized(), [
			'error_message' => "Manager with owner {{owner}} already initialized.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->isLazy() || isset($this->builder), [
			'error_message' => "No builder function set in manager with owner {{owner}}.",
			'hint_message' => "A builder function is required to be set, as lazy-loading is enabled.",
			'parameters' => ['owner' => $this->owner]
		]);
		
		//initialize remainder
		if (isset($remainder) || isset($this->remainderer)) {
			$remainder = [];
		}
		
		//persisted
		if ($persisted) {
			$this->flags |= self::FLAG_PERSISTED;
		}
		
		//initialize
		$lazy = $this->isLazy();
		$this->flags |= self::FLAG_INITIALIZING;
		try {
			//required (initialize)
			$required_map = [];
			if ($lazy) {
				$required_map = $this->required_map;
			} else {
				foreach ($this->properties as $name => $property) {
					if ($property->isRequired()) {
						$required_map[$name] = true;
					}
				}
			}
			$required_count = count($required_map);
			$required_names = array_keys($required_map);
			
			//required (process)
			if (!empty($required_map)) {
				//prepare
				foreach ($properties as $name => $value) {
					if (is_int($name) && isset($required_names[$name])) {
						$properties[$required_names[$name]] = $value;
						unset($properties[$name]);
					}
				}
				
				//process
				$missing_names = array_keys(array_diff_key($required_map, $properties));
				UCall::guardParameter('properties', $properties, empty($missing_names), [
					'error_message' => "Missing required property {{names}} for manager with owner {{owner}}.",
					'error_message_plural' => "Missing required properties {{names}} for manager with owner {{owner}}.",
					'error_message_number' => count($missing_names),
					'parameters' => ['names' => $missing_names, 'owner' => $this->owner],
					'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
				]);
			}
			
			//remainder properties
			if (isset($remainder)) {
				//remainder
				foreach ($properties as $name => $value) {
					if (is_int($name)) {
						$remainder[$name - $required_count] = $value;
						unset($properties[$name]);
					} elseif (!$this->hasProperty($name)) {
						$remainder[$name] = $value;
						unset($properties[$name]);
					}
				}
				
				//remainderer
				if (isset($this->remainderer)) {
					($this->remainderer)($remainder);
					$this->remainderer = null;
				}
			}
			
			//properties (set value)
			foreach ($properties as $name => $value) {
				//initialize
				$property = $this->getProperty($name);
				
				//guard
				UCall::guardParameter('properties', $properties, $property->getMode() !== 'r', [
					'error_message' => "Cannot set read-only property {{name}} in manager with owner {{owner}}.",
					'parameters' => ['name' => $name, 'owner' => $this->owner]
				]);
				
				//guard (persistence)
				if (!$persisted) {
					UCall::guardParameter('properties', $properties, !$property->isAutoImmutable(), [
						'error_message' => "Cannot set auto-immutable property {{name}} in manager " . 
							"with owner {{owner}}.",
						'hint_message' => "Auto-immutable properties cannot be set.",
						'parameters' => ['name' => $name, 'owner' => $this->owner]
					]);
					UCall::guardParameter('properties', $properties, !$property->isAutomatic(), [
						'error_message' => "Cannot set automatic property {{name}} in manager with owner {{owner}}.",
						'hint_message' => "Automatic properties may only be set after the first data persistence.",
						'parameters' => ['name' => $name, 'owner' => $this->owner]
					]);
				}
				
				//set
				UCall::guardParameter('properties', $properties, $property->setValue($value, false, true), [
					'error_message' => "Invalid value {{value}} for property {{name}} in manager with owner {{owner}}.",
					'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
				]);
			}
			
			//properties (finish)
			foreach ($this->properties as $name => $property) {
				//initialize
				if (!$lazy && !$property->isInitialized() && ($persisted || !$property->isAutomatic())) {
					$property->initialize();
				}
				
				//transient write-once
				if ($property->getMode() === 'w--') {
					unset($this->properties[$name]);
				}
			}
			
			//persistence
			if ($persisted) {
				$this->reloadPersistedValues();
			}
			
		} finally {
			$this->flags &= ~self::FLAG_INITIALIZING;
		}
		$this->flags |= self::FLAG_INITIALIZED;
		
		//return
		return $this;
	}
	
	/**
	 * Check if has property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name): bool
	{
		if ($this->hasProperty($name)) {
			return true;
		} elseif (isset($this->fallback_object)) {
			return $this->fallback_object->has($name);
		}
		return false;
	}
	
	/**
	 * Get property value with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set value without evaluating it, if currently set as such.</p>
	 * @return mixed
	 * <p>The property value with the given name.</p>
	 */
	final public function get(string $name, bool $lazy = false)
	{
		//fallback
		if (isset($this->fallback_object) && !$this->hasProperty($name)) {
			return $this->fallback_object->get($name);
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		UCall::guard($property_mode[0] === 'r', [
			'error_message' => "Cannot get write-only property {{name}} from manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($this->isPersisted() || !$property->isAutomatic(), [
			'error_message' => "Cannot get automatic property {{name}} in manager with owner {{owner}}.",
			'hint_message' => "Automatic properties may only be got after the first data persistence.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//return
		return $property->getValue($lazy);
	}
	
	/**
	 * Get boolean property value with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return bool
	 * <p>The boolean property value with the given name.</p>
	 */
	final public function is(string $name): bool
	{
		//fallback
		if (isset($this->fallback_object) && !$this->hasProperty($name)) {
			return $this->fallback_object->is($name);
		}
		
		//value
		$value = $this->get($name);
		UCall::guard(is_bool($value), [
			'error_message' => "Invalid value {{value}} in property {{name}} in manager with owner {{owner}}.",
			'hint_message' => "Only a boolean property is allowed be returned with this method.",
			'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
		]);
		
		//return
		return $value;
	}
	
	/**
	 * Check if property with a given name is set.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is set.</p>
	 */
	final public function isset(string $name): bool
	{
		if ($this->hasProperty($name)) {
			return $this->get($name) !== null;
		} elseif (isset($this->fallback_object)) {
			return $this->fallback_object->isset($name);
		}
		return false;
	}
	
	/**
	 * Check if property with a given name is loaded.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is loaded.</p>
	 */
	final public function loaded(string $name): bool
	{
		return isset($this->properties[$name]);
	}
	
	/**
	 * Check if property with a given name is defaulted.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is defaulted.</p>
	 */
	final public function defaulted(string $name): bool
	{
		return $this->fallback_object !== null && !$this->hasProperty($name)
			? $this->fallback_object->defaulted($name)
			: $this->getProperty($name)->isDefaulted();
	}
	
	/**
	 * Set property with a given name and value.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @param bool $force [default = false]
	 * <p>Force the given value to be fully evaluated and set, 
	 * even if the property with the given name is set as lazy.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value, bool $force = false): Properties
	{
		//fallback
		if (isset($this->fallback_object) && !$this->hasProperty($name)) {
			$this->fallback_object->set($name, $value, $force);
			return $this;
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		UCall::guard(!$this->isReadonly(), [
			'error_message' => "Cannot set property {{name}} in read-only manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'r' && $property_mode !== 'r+', [
			'error_message' => "Cannot set read-only property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'w-' && $property_mode !== 'w--', [
			'error_message' => "Cannot set write-once property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//guard (persistence)
		UCall::guard(!$property->isAutoImmutable(), [
			'error_message' => "Cannot set auto-immutable property {{name}} in manager with owner {{owner}}.",
			'hint_message' => "Auto-immutable properties cannot be set.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		if ($this->isPersisted()) {
			UCall::guard(!$property->isImmutable(), [
				'error_message' => "Cannot set immutable property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Immutable properties may only be set before the first data persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} else {
			UCall::guard(!$property->isAutomatic(), [
				'error_message' => "Cannot set automatic property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Automatic properties may only be set after the first data persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//set
		UCall::guardParameter('value', $value, $property->setValue($value, $force, true), [
			'error_message' => "Invalid value for property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//return
		return $this;
	}
	
	/**
	 * Unset property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name): Properties
	{
		//fallback
		if (isset($this->fallback_object) && !$this->hasProperty($name)) {
			$this->fallback_object->unset($name);
			return $this;
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		UCall::guard(!$this->isReadonly(), [
			'error_message' => "Cannot unset property {{name}} in read-only manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'r' && $property_mode !== 'r+', [
			'error_message' => "Cannot unset read-only property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'w-' && $property_mode !== 'w--', [
			'error_message' => "Cannot unset write-once property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard(!$property->isRequired(), [
			'error_message' => "Cannot unset required property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//guard (persistence)
		$persisted = $this->isPersisted();
		UCall::guard(!$property->isAutoImmutable(), [
			'error_message' => "Cannot unset auto-immutable property {{name}} in manager with owner {{owner}}.",
			'hint_message' => "Auto-immutable properties cannot be unset.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		if ($persisted) {
			UCall::guard(!$property->isImmutable(), [
				'error_message' => "Cannot unset immutable property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Immutable properties may only be unset before the first data persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} else {
			UCall::guard(!$property->isAutomatic(), [
				'error_message' => "Cannot unset automatic property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Automatic properties may only be unset after the first data persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//unset
		if ($persisted && array_key_exists($name, $this->persisted_values)) {
			$property->setValue($this->persisted_values[$name]);
		} else {
			$property->unsetValue();
		}
		
		//return
		return $this;
	}
	
	/**
	 * Get all properties.
	 * 
	 * If lazy-loading is enabled, then only the currently loaded properties are returned.<br>
	 * Only properties which allow read access are returned.
	 * 
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set values without evaluating them, if currently set as such.</p>
	 * @return array
	 * <p>All the properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll(bool $lazy = false): array
	{
		//properties
		$properties = [];
		foreach ($this->properties as $name => $property) {
			if ($property->getMode()[0] === 'r') {
				$properties[$name] = $property->getValue($lazy);
			}
		}
		
		//fallback
		if (isset($this->fallback_object)) {
			$properties += $this->fallback_object->getAll($lazy);
		}
		
		//return
		return $properties;
	}
	
	/**
	 * Get all initializeable properties.
	 * 
	 * If lazy-loading is enabled, then only the currently loaded properties are returned.<br>
	 * Only properties which are allowed to be initialized with are returned.
	 * 
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set values without evaluating them, if currently set as such.</p>
	 * @return array
	 * <p>All the initializeable properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAllInitializeable(bool $lazy = false): array
	{
		$properties = [];
		foreach ($this->properties as $name => $property) {
			if ($property->getMode() !== 'r') {
				$properties[$name] = $property->getValue($lazy);
			}
		}
		return $properties;
	}
	
	/**
	 * Persist properties with a given inserter function and updater function.
	 * 
	 * If lazy-loading is enabled, then only the currently loaded properties are persisted.
	 * 
	 * @param callable $inserter
	 * <p>The function to use to insert a new given set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $values): array</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property values to insert, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Automatic properties are not included in this set, being required to be automatically 
	 * generated during insertion.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The inserted property values, including all automatically generated ones not set in <var>$values</var>, 
	 * as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to reset their corresponding properties to their newly persisted values, 
	 * thus all automatically generated property values must be returned, whereas any other property value may 
	 * optionally be either returned or not, with any corresponding property keeping its current value if a new one is 
	 * not returned.<br>
	 * <br>
	 * Any returned property values that have no corresponding properties are ignored.</p>
	 * @param callable $updater
	 * <p>The function to use to update from a given old set of property values to a new given set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $old_values, array $new_values, array $changed_names): array</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $old_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property values to update from, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $new_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property values to update to, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string[] $changed_names</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The changed property names to update.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The updated property values, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to reset their corresponding properties to their newly persisted values, 
	 * thus any property value may optionally be either returned or not, with any corresponding property keeping its 
	 * current value if a new one is not returned.<br>
	 * <br>
	 * Any returned property values that have no corresponding properties are ignored.</p>
	 * @param bool $changes_only [default = false]
	 * <p>Include only changed property values, both old and new, during an update.</p>
	 * @param bool $recursive [default = false]
	 * <p>Persist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function persist(
		callable $inserter, callable $updater, bool $changes_only = false, bool $recursive = false
	): Properties
	{
		//initialize
		$persisted = $this->isPersisted();
		
		//assert
		UCall::assert('inserter', $inserter, function (array $values): array {});
		UCall::assert(
			'updater', $updater, function (array $old_values, array $new_values, array $changed_names): array {}
		);
		
		//values
		$old_values = $this->persisted_values;
		$new_values = $persistables = [];
		foreach ($this->properties as $name => $property) {
			if ($property->isInitialized() && !$property->hasLazyValue()) {
				$value = $property->getValue(true);
				$new_values[$name] = $value;
				if ($persisted && $property->isImmutable()) {
					$old_values[$name] = $value;
				}
				if (is_object($value) && UType::persistable($value)) {
					$persistables[$name] = $value;
				}
				unset($value);
			}
		}
		
		//recursive
		if ($recursive) {
			foreach ($persistables as $persistable) {
				UType::persist($persistable, true);
			}
		}
		
		//changes map
		$changes_map = [];
		if ($persisted) {
			foreach ($this->persisted_keys as $name => $key) {
				if (array_key_exists($name, $new_values) && UType::keyValue($new_values[$name], true) !== $key) {
					$changes_map[$name] = true;
				}
			}
		} else {
			$changes_map = array_fill_keys(array_keys(UData::filter($new_values, [null], 0)), true);
		}
		
		//check
		if (empty($changes_map)) {
			return $this;
		}
		
		//pre-persistence callbacks
		foreach ($this->pre_persistent_changes_callbacks as $name => $callbacks) {
			if (isset($changes_map[$name])) {
				$old_value = $old_values[$name] ?? null;
				$new_value = $new_values[$name];
				foreach ($callbacks as $callback) {
					$callback($old_value, $new_value);
				}
				unset($old_value, $new_value);
			}
		}
		
		//persist
		$values = [];
		if ($persisted) {
			$update_old_values = $old_values;
			$update_new_values = $new_values;
			if ($changes_only) {
				$update_old_values = array_intersect_key($update_old_values, $changes_map);
				$update_new_values = array_intersect_key($update_new_values, $changes_map);
			}
			$values = $updater($update_old_values, $update_new_values, array_keys($changes_map));
			unset($update_old_values, $update_new_values);
		} else {
			//insert
			$values = $inserter($new_values);
			
			//missing names
			$missing_names = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isAutomatic() && !array_key_exists($name, $values)) {
					$missing_names[] = $name;
				}
			}
			if (!empty($missing_names)) {
				UCall::haltExecution($inserter, [
					'value' => $values,
					'error_message' => "Missing automatically generated property {{names}} " . 
						"in manager with owner {{owner}}.",
					'error_message_plural' => "Missing automatically generated properties {{names}} " . 
						"in manager with owner {{owner}}.",
					'error_message_number' => count($missing_names),
					'parameters' => ['names' => $missing_names, 'owner' => $this->owner],
					'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
				]);
			}
			unset($missing_names);
		}
		
		//set
		foreach ($values as $name => $value) {
			if (isset($this->properties[$name])) {
				$this->properties[$name]->setValue($value);
			}
		}
		
		//post-persistence callbacks
		foreach ($this->post_persistent_changes_callbacks as $name => $callbacks) {
			if (isset($changes_map[$name])) {
				$old_value = $old_values[$name] ?? null;
				$new_value = $new_values[$name];
				foreach ($callbacks as $callback) {
					$callback($old_value, $new_value);
				}
				unset($old_value, $new_value);
			}
		}
		
		//finalize
		$this->flags |= self::FLAG_PERSISTED;
		$this->reloadPersistedValues();
		
		//return
		return $this;
	}
	
	/**
	 * Unpersist properties.
	 * 
	 * @param callable|null $deleter [default = null]
	 * <p>The function to use to delete an old given set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $values): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property values to delete, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unpersist(?callable $deleter = null): Properties
	{
		//check
		if (!$this->isPersisted()) {
			return $this;
		}
		
		//values
		$values = [];
		foreach ($this->properties as $name => $property) {
			if ($property->isInitialized() && !$property->hasLazyValue()) {
				$values[$name] = $property->getValue(true);
			}
		}
		
		//pre-persistence callbacks
		foreach ($this->pre_persistent_changes_callbacks as $name => $callbacks) {
			if (isset($values[$name])) {
				$value = $values[$name];
				foreach ($callbacks as $callback) {
					$callback($value, null);
				}
				unset($value);
			}
		}
		
		//delete
		if ($deleter !== null) {
			UCall::assert('deleter', $deleter, function (array $values): void {});
			$deleter($values);
		}
		
		//uninitialize
		foreach ($this->properties as $property) {
			if ($property->isAutomatic()) {
				$property->uninitialize();
			}
		}
		
		//post-persistence callbacks
		foreach ($this->post_persistent_changes_callbacks as $name => $callbacks) {
			if (isset($values[$name])) {
				$value = $values[$name];
				foreach ($callbacks as $callback) {
					$callback($value, null);
				}
				unset($value);
			}
		}
		
		//finalize
		$this->flags &= ~self::FLAG_PERSISTED;
		$this->clearPersistedValues();
		
		//return
		return $this;
	}
	
	/**
	 * Add pre-persistent property change callback function for a given property name.
	 * 
	 * All pre-persistent property change callback functions are called immediately before the corresponding property 
	 * value change is persisted or unpersisted.
	 * 
	 * @param string $name
	 * <p>The property name to add for.</p>
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($old_value, $new_value): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $old_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property value, to be persisted from.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value, to be persisted to.</p>
	 * @throws \Dracodeum\Kit\Managers\Properties\Exceptions\PropertyNotFound
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPrePersistentChangeCallback(string $name, callable $callback): Properties
	{
		//guard
		UCall::guard(!$this->isReadonly(), [
			'error_message' => "Cannot add pre-persistent property change callback for property {{name}} " . 
				"in read-only manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//validate
		$this->getProperty($name);
		UCall::assert('callback', $callback, function ($old_value, $new_value): void {});
		
		//add
		$this->pre_persistent_changes_callbacks[$name][] = \Closure::fromCallable($callback);
		
		//return
		return $this;
	}
	
	/**
	 * Add post-persistent property change callback function for a given property name.
	 * 
	 * All post-persistent property change callback functions are called immediately after the corresponding property 
	 * value change is persisted or unpersisted.
	 * 
	 * @param string $name
	 * <p>The property name to add for.</p>
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($old_value, $new_value): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $old_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property value, persisted from.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value, persisted to.</p>
	 * @throws \Dracodeum\Kit\Managers\Properties\Exceptions\PropertyNotFound
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPostPersistentChangeCallback(string $name, callable $callback): Properties
	{
		//guard
		UCall::guard(!$this->isReadonly(), [
			'error_message' => "Cannot add post-persistent property change callback for property {{name}} " . 
				"in read-only manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//validate
		$this->getProperty($name);
		UCall::assert('callback', $callback, function ($old_value, $new_value): void {});
		
		//add
		$this->post_persistent_changes_callbacks[$name][] = \Closure::fromCallable($callback);
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Check if has property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final protected function hasProperty(string $name): bool
	{
		return $this->getProperty($name, true) !== null;
	}
	
	/**
	 * Get property instance with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Managers\Properties\Exceptions\PropertyNotFound
	 * @return \Dracodeum\Kit\Managers\Properties\Property|null
	 * <p>The property instance with the given name.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final protected function getProperty(string $name, bool $no_throw = false): ?Property
	{
		if (!isset($this->properties[$name])) {
			//build
			$property = null;
			if (isset($this->builder)) {
				$property = ($this->builder)($name);
				if ($this->isInitialized() && isset($property) && !$property->isInitialized()) {
					$property->initialize();
				}
			}
			if (!isset($property)) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\PropertyNotFound([$this, $name]);
			}
			
			//property
			UCall::guardInternal($property->getName() === $name, [
				'error_message' => "Property name {{property.getName()}} mismatches the expected name {{name}}, " . 
					"in manager with owner {{owner}}.",
				'parameters' => ['property' => $property, 'name' => $name, 'owner' => $this->owner]
			]);
			UCall::guardInternal($property->getManager() === $this, [
				'error_message' => "Manager mismatch for property {{property.getName()}}, " . 
					"in manager with owner {{owner}}.",
				'hint_message' => "The manager which a given property is set with and the one it is being added to " . 
					"must be exactly the same.",
				'parameters' => ['property' => $property, 'owner' => $this->owner]
			]);
			$this->properties[$name] = $property;
		}
		return $this->properties[$name];
	}
	
	
	
	//Final private methods
	/**
	 * Reload persisted property values.
	 * 
	 * @return void
	 */
	final private function reloadPersistedValues(): void
	{
		$this->clearPersistedValues();
		foreach ($this->properties as $name => $property) {
			if ($property->isInitialized() && !$property->isImmutable() && !$property->isLazy()) {
				$value = $property->getValue(true);
				$this->persisted_values[$name] = $value;
				$this->persisted_keys[$name] = UType::keyValue($value, true);
			}
		}
	}
	
	/**
	 * Clear persisted property values.
	 * 
	 * @return void
	 */
	final private function clearPersistedValues(): void
	{
		$this->persisted_values = $this->persisted_keys = [];
	}
}
