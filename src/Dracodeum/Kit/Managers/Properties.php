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
	Uid as IUid,
	Properties as IProperties,
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
use Dracodeum\Kit\Utilities\{
	Call as UCall,
	Data as UData,
	Text as UText,
	Type as UType
};

/**
 * This manager handles and stores a separate set of properties for an object, which may be lazy-loaded and restricted 
 * to a specific mode of operation (strict read-only, read-only, read-write, write-only, write-once or 
 * transient write-once).
 * 
 * Each individual property may be set with restrictions, bindings and other characteristics, such as:<br>
 * &nbsp; &#8226; &nbsp; being restricted to a specific mode of operation;<br>
 * &nbsp; &#8226; &nbsp; having their own type or evaluator function to restrict the type of value it may hold;<br>
 * &nbsp; &#8226; &nbsp; having its own accessor functions (a getter or a setter or both);<br>
 * &nbsp; &#8226; &nbsp; being bound to an existing native object property;<br>
 * &nbsp; &#8226; &nbsp; having a default value or getter function;<br>
 * &nbsp; &#8226; &nbsp; being set as lazy.<br>
 * <br>
 * These properties may also be persisted to any data storage of choice, through the addition and implementation of 
 * persistence functions to both insert and update property values, with each individual property able to be defined as 
 * automatic (automatically generated value during insert, disallowing the value to be set before insertion) or 
 * immutable (disallowing the value to be modified after insertion) or both.
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
	
	/** @var string[] */
	private $aliases = [];
	
	/** @var \Closure|null */
	private $builder = null;
	
	/** @var \Closure|null */
	private $remainderer = null;
	
	/** @var \Dracodeum\Kit\Managers\Properties\Property[] */
	private $properties = [];
	
	/** @var \Dracodeum\Kit\Interfaces\Properties|null */
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
		if (!in_array($mode, self::MODES, true)) {
			UCall::haltParameter('mode', $mode, [
				'hint_message' => "Only the following modes are allowed in manager with owner {{owner}}: {{modes}}.",
				'parameters' => ['owner' => $owner, 'modes' => self::MODES],
				'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
			]);
		}
		
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
			$properties[$name] = $property->isGettable() ? $property->getValue(true) : null;
		}
		
		//set
		if (System::getDumpVerbosityLevel() >= EDumpVerbosityLevel::MEDIUM) {
			foreach ($properties as $name => $value) {
				//initialize
				$property = $this->getProperty($name);
				$property_mode = $property->getMode();
				$property_debug_name = "{$property_mode}:{$name}";
				
				//persistence
				if ($property->isAutoImmutable()) {
					$property_debug_name = "auto-immutable {$property_debug_name}";
				} elseif ($property->isImmutable()) {
					$property_debug_name = "immutable {$property_debug_name}";
				} elseif ($property->isAutomatic()) {
					$property_debug_name = "automatic {$property_debug_name}";
				} elseif ($property->isVolatile()) {
					$property_debug_name = "volatile {$property_debug_name}";
				}
				
				//state
				if (!$property->isGettable()) {
					$property_debug_name = "[unset] {$property_debug_name}";
				} elseif ($property->hasLazyValue()) {
					$property_debug_name = "[lazy] {$property_debug_name}";
				}
				
				//read-only
				if ($this->isReadonly()) {
					$property_debug_name_prefix = $property_mode[0] === 'r' ? '(readonly)' : '(locked)';
					$property_debug_name = "{$property_debug_name_prefix} {$property_debug_name}";
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
			if ($property->isGettable()) {
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
		$this->guardInitializedCall();
		$this->flags |= self::FLAG_READONLY;
		return $this;
	}
	
	/**
	 * Add required property name.
	 * 
	 * The property, corresponding to the given name added here, must be given during initialization.<br>
	 * <br>
	 * This method may only be called before initialization and with lazy-loading enabled.
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
	 * This method may only be called before initialization and with lazy-loading enabled.
	 * 
	 * @param string[] $names
	 * <p>The names to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names): Properties
	{
		//guard
		$this->guardNonInitializedCall();
		if (!$this->isLazy()) {
			UCall::halt([
				'hint_message' => "Lazy-loading is disabled in manager with owner {{owner}}, " . 
					"therefore any property is implicitly required if it is not explicitly set as optional.",
				'parameters' => ['owner' => $this->owner]
			]);
		}
		
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
		if ($this->isLazy()) {
			$this->processPropertyNameAlias($name);
			return isset($this->required_map[$name]);
		}
		return $this->getProperty($name)->isRequired();
	}
	
	/**
	 * Add property alias.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @param string $alias
	 * <p>The alias to add.</p>
	 * @param string $name
	 * <p>The name to add for.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPropertyAlias(string $alias, string $name): Properties
	{
		return $this->addPropertyAliases([$alias => $name]);
	}
	
	/**
	 * Add property aliases.
	 * 
	 * This method may only be called before initialization.
	 * 
	 * @param string[] $aliases
	 * <p>The aliases to add, as <samp>alias => name</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPropertyAliases(array $aliases): Properties
	{
		$this->guardNonInitializedCall();
		$this->aliases += $aliases;
		return $this;
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
		$this->guardNonInitializedCall();
		if ($this->isLazy()) {
			UCall::halt([
				'hint_message' => "Lazy-loading is enabled in manager with owner {{owner}}, " . 
					"therefore in order to add new properties please set and use a builder function instead.",
				'parameters' => ['owner' => $this->owner]
			]);
		} elseif (isset($this->properties[$name])) {
			UCall::haltParameter('name', $name, [
				'error_message' => "A property {{name}} has already been added to manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//property
		$property = $this->createProperty($name);
		if ($property->getName() !== $name) {
			UCall::haltInternal([
				'error_message' => "Property name {{property.getName()}} mismatches the expected name {{name}} " . 
					"in manager with owner {{owner}}.",
				'parameters' => ['property' => $property, 'name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property->getManager() !== $this) {
			UCall::haltInternal([
				'error_message' => "Manager mismatch for property {{property.getName()}} " . 
					"in manager with owner {{owner}}.",
				'hint_message' => "The manager which a given property is set with and the one it is being added to " . 
					"must be exactly the same.",
				'parameters' => ['property' => $property, 'owner' => $this->owner]
			]);
		}
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
		//guard
		$this->guardNonInitializedCall();
		if (!$this->isLazy()) {
			UCall::halt([
				'hint_message' => "Lazy-loading is disabled in manager with owner {{owner}}, " . 
					"therefore a builder function cannot be set.",
				'parameters' => ['owner' => $this->owner]
			]);
		}
		
		//set
		UCall::assert('builder', $builder, function (string $name): ?Property {});
		$this->builder = \Closure::fromCallable($builder);
		
		//return
		return $this;
	}
	
	/**
	 * Set remainderer function.
	 * 
	 * A remainderer function is used to handle any remaining properties which were not found during initialization, 
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
		$this->guardNonInitializedCall();
		UCall::assert('remainderer', $remainderer, function (array $properties): void {});
		$this->remainderer = \Closure::fromCallable($remainderer);
		return $this;
	}
	
	/**
	 * Set fallback object.
	 * 
	 * By setting a fallback object, any property not found in this instance is attempted to be retrieved from 
	 * the given fallback object instead.
	 * 
	 * @param \Dracodeum\Kit\Interfaces\Properties $object
	 * <p>The object to set, as an object implementing the <code>Dracodeum\Kit\Interfaces\Properties</code> 
	 * interface.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setFallbackObject(IProperties $object): Properties
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
	 * <p>The properties remainder.<br>
	 * If set, then it is filled with all remaining properties which have not been found from the given 
	 * <var>$properties</var> above, as <samp>name => value</samp> pairs or 
	 * an array of required property values or both.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(
		array $properties = [], bool $persisted = false, ?array &$remainder = null
	): Properties
	{
		//guard
		$this->guardNonInitializedCall();
		if ($this->isLazy() && $this->builder === null) {
			UCall::halt([
				'error_message' => "No builder function set in manager with owner {{owner}}.",
				'hint_message' => "Lazy-loading is enabled in manager with owner {{owner}}, " . 
					"therefore a builder function must be set.",
				'parameters' => ['owner' => $this->owner]
			]);
		}
		
		//remainder (initialize)
		if ($remainder !== null || $this->remainderer !== null) {
			$remainder = [];
		}
		
		//persisted
		if ($persisted) {
			$this->flags |= self::FLAG_PERSISTED;
		}
		
		//initialize
		$this->flags |= self::FLAG_INITIALIZING;
		try {
			//aliases
			$this->processPropertyValuesAliases($properties);
			
			//required (initialize)
			$required_map = [];
			if ($this->isLazy()) {
				$required_map = $this->required_map;
			} else {
				foreach ($this->properties as $name => $property) {
					if ($property->isRequired()) {
						$required_map[$name] = true;
					}
				}
			}
			$required_count = count($required_map);
			
			//required (process)
			if ($required_count) {
				//remap
				$required_names = array_keys($required_map);
				foreach ($properties as $name => $value) {
					if (is_int($name) && isset($required_names[$name])) {
						$properties[$required_names[$name]] = $value;
						unset($properties[$name]);
					}
				}
				
				//missing
				$missing_names = array_keys(array_diff_key($required_map, $properties));
				$missing_names_count = count($missing_names);
				if ($missing_names_count) {
					UCall::haltParameter('properties', $properties, [
						'error_message' => "Missing required property {{names}} for manager with owner {{owner}}.",
						'error_message_plural' => "Missing required properties {{names}} " . 
							"for manager with owner {{owner}}.",
						'error_message_number' => $missing_names_count,
						'parameters' => ['names' => $missing_names, 'owner' => $this->owner],
						'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
					]);
				}
			}
			
			//remainder (properties)
			if ($remainder !== null) {
				//process
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
				if ($this->remainderer !== null) {
					($this->remainderer)($remainder);
					$this->remainderer = null;
				}
			}
			
			//properties (set value)
			foreach ($properties as $name => $value) {
				//property
				$property = $this->getProperty($name);
				
				//guard
				if ($property->getMode() === 'r') {
					UCall::haltParameter('properties', $properties, [
						'error_message' => "Cannot set read-only property {{name}} in manager with owner {{owner}}.",
						'parameters' => ['name' => $name, 'owner' => $this->owner]
					]);
				}
				
				//guard (persistence)
				if (!$persisted) {
					if ($property->isAutoImmutable()) {
						UCall::haltParameter('properties', $properties, [
							'error_message' => "Cannot set auto-immutable property {{name}} in manager " . 
								"with owner {{owner}}.",
							'hint_message' => "Auto-immutable properties cannot be set.",
							'parameters' => ['name' => $name, 'owner' => $this->owner]
						]);
					} elseif ($property->isAutomatic()) {
						UCall::haltParameter('properties', $properties, [
							'error_message' => "Cannot set automatic property {{name}} in manager " . 
								"with owner {{owner}}.",
							'hint_message' => "Automatic properties may only be set after the first persistence.",
							'parameters' => ['name' => $name, 'owner' => $this->owner]
						]);
					}
				}
				
				//set
				if (!$property->setValue($value, false, true)) {
					UCall::haltParameter('properties', $properties, [
						'error_message' => "Invalid value {{value}} for property {{name}} in manager " . 
							"with owner {{owner}}.",
						'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
					]);
				}
			}
			
			//properties (finalize)
			foreach ($this->properties as $name => $property) {
				if ($property->getMode() === 'w--') {
					unset($this->properties[$name]);
				} elseif (!$property->isInitialized()) {
					$property->initialize();
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
	 * Check if has a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the property with the given name.</p>
	 */
	final public function has(string $name): bool
	{
		if ($this->hasProperty($name)) {
			return true;
		} elseif ($this->fallback_object !== null) {
			return $this->fallback_object->has($name);
		}
		return false;
	}
	
	/**
	 * Get value from a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set value without evaluating it, if currently set as such.</p>
	 * @return mixed
	 * <p>The value from the property with the given name.</p>
	 */
	final public function get(string $name, bool $lazy = false)
	{
		//fallback
		if ($this->fallback_object !== null && !$this->hasProperty($name)) {
			return $this->fallback_object->get($name, $lazy);
		}
		
		//property
		$property = $this->getProperty($name);
		if ($property->getMode()[0] !== 'r') {
			UCall::halt([
				'error_message' => "Cannot get write-only property {{name}} from manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//return
		return $property->getValue($lazy);
	}
	
	/**
	 * Get boolean value from a property with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, however it only returns a boolean property value, 
	 * and is used to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return bool
	 * <p>The boolean value from the property with the given name.</p>
	 */
	final public function is(string $name): bool
	{
		//fallback
		if ($this->fallback_object !== null && !$this->hasProperty($name)) {
			return $this->fallback_object->is($name);
		}
		
		//value
		$value = $this->get($name);
		if (!is_bool($value)) {
			UCall::halt([
				'error_message' => "Invalid value {{value}} in property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Only a boolean value is allowed be returned by this method.",
				'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
			]);
		}
		
		//return
		return $value;
	}
	
	/**
	 * Check if a property with a given name is set.
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
		} elseif ($this->fallback_object !== null) {
			return $this->fallback_object->isset($name);
		}
		return false;
	}
	
	/**
	 * Check if a property with a given name is loaded.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is loaded.</p>
	 */
	final public function loaded(string $name): bool
	{
		$this->processPropertyNameAlias($name);
		return isset($this->properties[$name]);
	}
	
	/**
	 * Check if a property with a given name is initialized.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is initialized.</p>
	 */
	final public function initialized(string $name): bool
	{
		return $this->fallback_object !== null && !$this->hasProperty($name)
			? $this->fallback_object->initialized($name)
			: $this->getProperty($name)->isInitialized();
	}
	
	/**
	 * Check if a property with a given name is gettable.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is gettable.</p>
	 */
	final public function gettable(string $name): bool
	{
		return $this->fallback_object !== null && !$this->hasProperty($name)
			? $this->fallback_object->gettable($name)
			: $this->getProperty($name)->isGettable();
	}
	
	/**
	 * Check if a property with a given name is settable.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is settable.</p>
	 */
	final public function settable(string $name): bool
	{
		return $this->fallback_object !== null && !$this->hasProperty($name)
			? $this->fallback_object->settable($name)
			: $this->getProperty($name)->isSettable();
	}
	
	/**
	 * Check if a property with a given name is defaulted.
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
	 * Evaluate a given value with a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to evaluate with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated with the property 
	 * with the given name.</p>
	 */
	final public function eval(string $name, &$value): bool
	{
		return $this->fallback_object !== null && !$this->hasProperty($name)
			? $this->fallback_object->eval($name, $value)
			: $this->getProperty($name)->evaluateValue($value);
	}
	
	/**
	 * Set value in a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $force [default = false]
	 * <p>Force the given value to be fully evaluated and set, 
	 * even if the property with the given name is set as lazy.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value, bool $force = false): Properties
	{
		//fallback
		if ($this->fallback_object !== null && !$this->hasProperty($name)) {
			$this->fallback_object->set($name, $value, $force);
			return $this;
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		if ($this->isReadonly()) {
			UCall::halt([
				'error_message' => "Cannot set value in property {{name}} in read-only manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property_mode === 'r' || $property_mode === 'r+') {
			UCall::halt([
				'error_message' => "Cannot set value in read-only property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property_mode === 'w-' || $property_mode === 'w--') {
			UCall::halt([
				'error_message' => "Cannot set value in write-once property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//guard (persistence)
		$persisted = $this->isPersisted();
		if ($property->isAutoImmutable()) {
			UCall::halt([
				'error_message' => "Cannot set value in auto-immutable property {{name}} in manager " . 
					"with owner {{owner}}.",
				'hint_message' => "Auto-immutable properties cannot be set.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif (!$persisted && $property->isAutomatic()) {
			UCall::halt([
				'error_message' => "Cannot set value in automatic property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Automatic properties may only be set after the first persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($persisted && $property->isImmutable()) {
			UCall::halt([
				'error_message' => "Cannot set value in immutable property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Immutable properties may only be set before the first persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//set
		if (!$property->setValue($value, $force, true)) {
			UCall::haltParameter('value', $value, [
				'error_message' => "Invalid value for property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset value in a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name): Properties
	{
		//fallback
		if ($this->fallback_object !== null && !$this->hasProperty($name)) {
			$this->fallback_object->unset($name);
			return $this;
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		if ($this->isReadonly()) {
			UCall::halt([
				'error_message' => "Cannot unset value in property {{name}} in read-only manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property_mode === 'r' || $property_mode === 'r+') {
			UCall::halt([
				'error_message' => "Cannot unset value in read-only property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property_mode === 'w-' || $property_mode === 'w--') {
			UCall::halt([
				'error_message' => "Cannot unset value in write-once property {{name}} in manager " . 
					"with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//guard (persistence)
		$persisted = $this->isPersisted();
		if ($property->isAutoImmutable()) {
			UCall::halt([
				'error_message' => "Cannot unset value in auto-immutable property {{name}} in manager " . 
					"with owner {{owner}}.",
				'hint_message' => "Auto-immutable properties cannot be unset.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif (!$persisted && $property->isAutomatic()) {
			UCall::halt([
				'error_message' => "Cannot unset value in automatic property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Automatic properties may only be unset after the first persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($persisted && $property->isImmutable()) {
			UCall::halt([
				'error_message' => "Cannot unset value in immutable property {{name}} in manager with owner {{owner}}.",
				'hint_message' => "Immutable properties may only be unset before the first persistence.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//unset
		$property_name = $property->getName();
		if ($persisted && array_key_exists($property_name, $this->persisted_values)) {
			$property->setValue($this->persisted_values[$property_name]);
		} elseif ($property->isRequired()) {
			UCall::halt([
				'error_message' => "Cannot unset value in required property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
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
			if ($property->isGettable() && $property->getMode()[0] === 'r') {
				$properties[$name] = $property->getValue($lazy);
			}
		}
		
		//fallback
		if ($this->fallback_object !== null) {
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
		$persisted = $this->isPersisted();
		foreach ($this->properties as $name => $property) {
			if (
				$property->isGettable() && $property->getMode() !== 'r' && $property->isSettable() && 
				($persisted || !$property->isAutomatic())
			) {
				$properties[$name] = $property->getValue($lazy);
			}
		}
		return $properties;
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
						$property->isGettable() && !$property->hasLazyValue() && 
						!UType::persistedValue($property->getValue(), true, true)
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
	 * Reload persisted properties with a given loader function.
	 * 
	 * If lazy-loading is enabled, then only the currently loaded properties are reloaded.
	 * 
	 * @param callable $loader
	 * <p>The function to use to load a set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): array</code><br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The persisted property values, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to set their corresponding properties with their persisted values, 
	 * with any property keeping its current value if a new one is not returned.<br>
	 * <br>
	 * Any returned property values which have no corresponding properties are ignored.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function reload(callable $loader): Properties
	{
		//check
		UCall::assert('loader', $loader, function (): array {});
		if (!$this->isPersisted()) {
			return $this;
		}
		
		//reload
		$this->setPersistedPropertyValues($loader());
		$this->reloadPersistedValues();
		
		//return
		return $this;
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
	 * &nbsp; &nbsp; &nbsp; Automatic properties may not be included in this set, in which case they are required to be 
	 * automatically generated during insertion.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The inserted property values, including all automatically generated ones not set in <var>$values</var>, 
	 * as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to set their corresponding properties with their newly persisted values, 
	 * therefore all automatically generated property values must be returned, whereas any other property value may 
	 * be either returned or not, with any property keeping its current value if a new one is not returned.<br>
	 * <br>
	 * Any returned property values which have no corresponding properties are ignored.</p>
	 * @param callable $updater
	 * <p>The function to use to update from an old given set of property values to a new given set.<br>
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
	 * All returned property values are used to set their corresponding properties with their newly persisted values, 
	 * therefore any property value may be either returned or not, with any property keeping its current value if a new 
	 * one is not returned.<br>
	 * <br>
	 * Any returned property values which have no corresponding properties are ignored.</p>
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
		//assert
		UCall::assert('inserter', $inserter, function (array $values): array {});
		UCall::assert(
			'updater', $updater, function (array $old_values, array $new_values, array $changed_names): array {}
		);
		
		//recursive
		if ($recursive) {
			foreach ($this->properties as $property) {
				if ($property->isGettable() && !$property->hasLazyValue()) {
					$value = $property->getValue();
					if (is_object($value) && UType::persistable($value)) {
						UType::persist($value, true);
					}
					unset($value);
				}
			}
		}
		
		//changes map
		$old_values = $new_values = [];
		$changes_map = $this->getPersistenceChangesMap($old_values, $new_values);
		if (!count($changes_map)) {
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
		if ($this->isPersisted()) {
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
			
			//aliases
			$this->processPropertyValuesAliases($values);
			
			//missing names (process)
			$missing_names = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isAutomatic() && !$property->isGettable() && !array_key_exists($name, $values)) {
					$missing_names[] = $name;
				}
			}
			
			//missing names (check)
			$missing_names_count = count($missing_names);
			if ($missing_names_count) {
				UCall::haltExecution($inserter, [
					'value' => $values,
					'error_message' => "Missing automatically generated property {{names}} " . 
						"in manager with owner {{owner}}.",
					'error_message_plural' => "Missing automatically generated properties {{names}} " . 
						"in manager with owner {{owner}}.",
					'error_message_number' => $missing_names_count,
					'parameters' => ['names' => $missing_names, 'owner' => $this->owner],
					'string_options' => ['non_assoc_mode' => UText::STRING_NONASSOC_MODE_COMMA_LIST_AND]
				]);
			}
			unset($missing_names, $missing_names_count);
		}
		
		//set
		$this->setPersistedPropertyValues($values);
		
		//post-persistence callbacks
		if (count($this->post_persistent_changes_callbacks)) {
			//changes map
			$old_values = $new_values = [];
			$changes_map = $this->getPersistenceChangesMap(
				$old_values, $new_values, array_keys($this->post_persistent_changes_callbacks)
			);
			
			//callbacks
			$changes_callbacks = array_intersect_key($this->post_persistent_changes_callbacks, $changes_map);
			foreach ($changes_callbacks as $name => $callbacks) {
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
	 * <p>The function to use to delete a given old set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $values): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property values to delete, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @param bool $recursive [default = false]
	 * <p>Unpersist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unpersist(?callable $deleter = null, bool $recursive = false): Properties
	{
		//unpersistables (initialize)
		$unpersistables = [];
		if ($recursive) {
			foreach ($this->properties as $name => $property) {
				if ($property->isGettable()) {
					$value = $property->getValue();
					if (is_object($value) && UType::unpersistable($value)) {
						$unpersistables[$name] = $value;
					}
					unset($value);
				}
			}
		}
		
		//persisted
		if ($this->isPersisted()) {
			//values
			$values = [];
			foreach ($this->properties as $name => $property) {
				if ($property->isGettable() && !$property->isVolatile()) {
					$value = $property->getValue();
					$values[$name] = is_object($value) && UType::persistable($value) && $value instanceof IUid
						? $value->getUid()
						: $value;
					unset($value);
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
			
			//automatic
			foreach ($this->properties as $property) {
				if ($property->isAutomatic()) {
					$property->unsetValue();
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
		}
		
		//unpersistables (unpersist)
		if ($recursive && count($unpersistables)) {
			UType::unpersistValue($unpersistables, true);
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
	 * &nbsp; &nbsp; &nbsp; The old property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of a deletion.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPrePersistentChangeCallback(string $name, callable $callback): Properties
	{
		//guard
		$property = $this->getProperty($name);
		if ($this->isReadonly()) {
			UCall::halt([
				'error_message' => "Cannot add pre-persistent property change callback for property {{name}} " . 
					"in read-only manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property->isVolatile()) {
			UCall::halt([
				'error_message' => "Cannot add pre-persistent property change callback for " . 
					"volatile property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//add
		UCall::assert('callback', $callback, function ($old_value, $new_value): void {});
		$this->pre_persistent_changes_callbacks[$property->getName()][] = \Closure::fromCallable($callback);
		
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
	 * &nbsp; &nbsp; &nbsp; The old property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of a deletion.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addPostPersistentChangeCallback(string $name, callable $callback): Properties
	{
		//guard
		$property = $this->getProperty($name);
		if ($this->isReadonly()) {
			UCall::halt([
				'error_message' => "Cannot add post-persistent property change callback for property {{name}} " . 
					"in read-only manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		} elseif ($property->isVolatile()) {
			UCall::halt([
				'error_message' => "Cannot add post-persistent property change callback for " . 
					"volatile property {{name}} in manager with owner {{owner}}.",
				'parameters' => ['name' => $name, 'owner' => $this->owner]
			]);
		}
		
		//add
		UCall::assert('callback', $callback, function ($old_value, $new_value): void {});
		$this->post_persistent_changes_callbacks[$property->getName()][] = \Closure::fromCallable($callback);
		
		//return
		return $this;
	}
	
	
	
	//Final protected methods
	/**
	 * Check if has a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the property with the given name.</p>
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
		$property_name = $name;
		$this->processPropertyNameAlias($name);
		if (!isset($this->properties[$name])) {
			//build
			$property = null;
			if ($this->builder !== null) {
				$property = ($this->builder)($name);
				if ($this->isInitialized() && $property !== null && !$property->isInitialized()) {
					$property->initialize();
				}
			}
			
			//check
			if ($property === null) {
				if ($no_throw) {
					return null;
				}
				throw new Exceptions\PropertyNotFound([$this, $property_name]);
			}
			
			//guard
			if ($property->getName() !== $name) {
				UCall::haltInternal([
					'error_message' => "Property name {{property.getName()}} mismatches the expected name {{name}}, " . 
						"in manager with owner {{owner}}.",
					'parameters' => ['property' => $property, 'name' => $name, 'owner' => $this->owner]
				]);
			} elseif ($property->getManager() !== $this) {
				UCall::haltInternal([
					'error_message' => "Manager mismatch for property {{property.getName()}}, " . 
						"in manager with owner {{owner}}.",
					'hint_message' => "The manager which a given property is set with and the one it is being added " . 
						"to must be exactly the same.",
					'parameters' => ['property' => $property, 'owner' => $this->owner]
				]);
			}
			
			//finalize
			$this->properties[$name] = $property;
		}
		return $this->properties[$name];
	}
	
	/**
	 * Process alias of a given property name.
	 * 
	 * @param string $name [reference]
	 * <p>The property name to process.</p>
	 * @return void
	 */
	final protected function processPropertyNameAlias(string &$name): void
	{
		if (isset($this->aliases[$name])) {
			$name = $this->aliases[$name];
		}
	}
	
	/**
	 * Process aliases of a given set of property values.
	 * 
	 * @param array $values [reference]
	 * <p>The property values to process, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final protected function processPropertyValuesAliases(array &$values): void
	{
		foreach ($this->aliases as $alias => $name) {
			if (array_key_exists($alias, $values)) {
				if (!array_key_exists($name, $values)) {
					$values[$name] = $values[$alias];
				}
				unset($values[$alias]);
			}
		}
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called if this instance is not initialized.
	 * 
	 * @return void
	 */
	final protected function guardNonInitializedCall(): void
	{
		if ($this->isInitialized()) {
			UCall::halt([
				'hint_message' => "This method may only be called before initialization, " . 
					"in manager with owner {{owner}}.",
				'parameters' => ['owner' => $this->owner],
				'stack_offset' => 1
			]);
		}
	}
	
	/**
	 * Guard the current function or method in the stack so it may only be called if this instance is initialized.
	 * 
	 * @return void
	 */
	final protected function guardInitializedCall(): void
	{
		if (!$this->isInitialized()) {
			UCall::halt([
				'hint_message' => "This method may only be called after initialization, " . 
					"in manager with owner {{owner}}.",
				'parameters' => ['owner' => $this->owner],
				'stack_offset' => 1
			]);
		}
	}
	
	
	
	//Final private methods
	/**
	 * Get persistence property changes map.
	 * 
	 * @param array $old_values [reference output] [default = []]
	 * <p>The processed old property values, as <samp>name => value</samp> pairs.</p>
	 * @param array $new_values [reference output] [default = []]
	 * <p>The processed new property values, as <samp>name => value</samp> pairs.</p>
	 * @param array|null $names [default = null]
	 * <p>The property names to restrict the map to.</p>
	 * @return array
	 * <p>The persistence property changes map, as <samp>name => <code>true</code></samp> pairs.</p>
	 */
	final private function getPersistenceChangesMap(
		array &$old_values = [], array &$new_values = [], ?array $names = null
	): array
	{
		//initialize
		$new_values = [];
		$old_values = $this->persisted_values;
		$old_keys = $this->persisted_keys;
		$persisted = $this->isPersisted();
		
		//names map
		$names_map = $names !== null ? array_flip($names) : null;
		if ($names_map !== null) {
			if (!count($names_map)) {
				$old_values = [];
				return [];
			}
			$old_values = array_intersect_key($old_values, $names_map);
			$old_keys = array_intersect_key($old_keys, $names_map);
		}
		
		//properties
		foreach ($this->properties as $name => $property) {
			if (
				$property->isGettable() && !$property->isVolatile() && 
				($names_map === null || isset($names_map[$name]))
			) {
				//initialize
				$value = $property->getValue();
				$new_values[$name] = $value;
				
				//lazy
				if ($persisted && $property->isLazy() && array_key_exists($name, $old_values)) {
					$property->evaluateValue($old_values[$name]);
				}
				
				//persistable (new uid)
				if (is_object($value) && UType::persistable($value) && $value instanceof IUid) {
					$new_values[$name] = $value->getUid();
				}
				
				//persistable (old uid)
				if ($persisted) {
					$old_value = $old_values[$name] ?? null;
					if (is_object($old_value) && UType::persistable($old_value) && $old_value instanceof IUid) {
						$old_uid = $old_value->getUid();
						$old_values[$name] = $old_uid;
						$old_keys[$name] = UType::keyValue($old_uid, true);
						unset($old_uid);
					}
					unset($old_value);
				}
				
				//finalize
				unset($value);
			}
		}
		
		//map
		if ($persisted) {
			$map = [];
			foreach ($old_keys as $name => $key) {
				if (array_key_exists($name, $new_values) && UType::keyValue($new_values[$name], true) !== $key) {
					$map[$name] = true;
				}
			}
			return $map;
		}
		return array_fill_keys(array_keys(UData::filter($new_values, [null], 0)), true);
	}
	
	/**
	 * Set persisted property values.
	 * 
	 * @param array $values
	 * <p>The property values to set, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	final private function setPersistedPropertyValues(array $values): void
	{
		foreach ($values as $name => $value) {
			$this->processPropertyNameAlias($name);
			$property = $this->properties[$name] ?? null;
			if ($property !== null && $property->isSettable() && !$property->isVolatile()) {
				$property->setValue($value);
			}
		}
	}
	
	/**
	 * Reload persisted property values.
	 * 
	 * @return void
	 */
	final private function reloadPersistedValues(): void
	{
		$this->clearPersistedValues();
		foreach ($this->properties as $name => $property) {
			if ($property->isGettable() && !$property->isVolatile()) {
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
