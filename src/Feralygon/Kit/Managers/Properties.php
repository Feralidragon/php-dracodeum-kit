<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\{
	Manager,
	Traits
};
use Feralygon\Kit\Interfaces\DebugInfo as IDebugInfo;
use Feralygon\Kit\Traits\DebugInfo\Info as DebugInfo;
use Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor as IDebugInfoProcessor;
use Feralygon\Kit\Managers\Properties\{
	Property,
	Exceptions
};
use Feralygon\Kit\Root\System;
use Feralygon\Kit\Root\System\Enumerations\DumpVerbosityLevel as EDumpVerbosityLevel;
use Feralygon\Kit\Interfaces\Propertiesable as IPropertiesable;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText
};

/**
 * This manager handles and stores a separate set of properties for an object, which may be lazy-loaded 
 * and restricted to a specific access mode (strict read-only, read-only, read-write, write-only and write-once).
 * 
 * Each individual property may be set with restrictions and bindings, such as being set as required, 
 * restricted to a specific access mode, bound to existing object properties, have a default value, 
 * have their own accessors (a getter and a setter) and their own type or evaluator to limit the type of values 
 * each one may hold.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Managers\Properties\Property
 */
class Properties extends Manager implements IDebugInfo, IDebugInfoProcessor
{
	//Traits
	use Traits\DebugInfo;
	
	
	
	//Public constants
	/** Allowed modes. */
	public const MODES = ['r', 'r+', 'rw', 'w', 'w-'];
	
	
	
	//Private properties
	/** @var object */
	private $owner;
	
	/** @var bool */
	private $lazy = false;
	
	/** @var string */
	private $mode = 'rw';
	
	/** @var bool */
	private $initialized = false;
	
	/** @var bool */
	private $initializing = false;
	
	/** @var bool[] */
	private $required_map = [];
	
	/** @var \Closure|null */
	private $builder = null;
	
	/** @var \Closure|null */
	private $remainderer = null;
	
	/** @var \Feralygon\Kit\Managers\Properties\Property[] */
	private $properties = [];
	
	/** @var \Feralygon\Kit\Interfaces\Propertiesable|null */
	private $fallback_object = null;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner
	 * <p>The owner object to instantiate with.</p>
	 * @param bool $lazy [default = false] 
	 * <p>Use lazy-loading, so that each property is only loaded on access.<br>
	 * <br>
	 * NOTE: With lazy-loading, the existence of each property becomes unknown ahead of time, 
	 * therefore when retrieving all properties, only the currently loaded ones are returned.</p>
	 * @param string $mode [default = 'rw']
	 * <p>The base access mode to set for all properties, which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow all properties to be only strictly read from, 
	 * so that they cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow all properties to be only read from (read-only), 
	 * although they may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow all properties to be both read from 
	 * and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow all properties to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow all properties to be only written to, 
	 * but only once during initialization (write-once).<br>
	 * <br>
	 * All properties default to the mode defined here, but if another mode is set, it becomes restricted as so:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp> or <samp>w-</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp> and <samp>w-</samp> are allowed.</p>
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
		$this->lazy = $lazy;
		$this->mode = $mode;
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Traits\DebugInfo\Interfaces\DebugInfoProcessor)
	/** {@inheritdoc} */
	public function processDebugInfo(DebugInfo $info): void
	{
		$properties = $this->getAll();
		if (System::getDumpVerbosityLevel() >= EDumpVerbosityLevel::MEDIUM) {
			foreach ($properties as $name => $value) {
				$info->set("{$this->getProperty($name)->getMode()}:{$name}", $value);
			}
		} else {
			$info->setAll($properties);
		}
	}
	
	
	
	//Public methods
	/**
	 * Create a property instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to create with.</p>
	 * @return \Feralygon\Kit\Managers\Properties\Property
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if lazy-loading is enabled.</p>
	 */
	final public function isLazy(): bool
	{
		return $this->lazy;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string
	 * <p>The mode.</p>
	 */
	final public function getMode(): string
	{
		return $this->mode;
	}
	
	/**
	 * Check if properties are read-only.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if properties are read-only.</p>
	 */
	final public function isReadonly(): bool
	{
		return $this->mode === 'r' || $this->mode === 'r+';
	}
	
	/**
	 * Set properties as read-only.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAsReadonly(): Properties
	{
		if (!$this->isReadonly()) {
			foreach ($this->properties as $name => $property) {
				$mode = $property->getMode();
				if ($mode !== 'r') {
					if ($mode[0] === 'r') {
						$property->setMode('r');
					} else {
						unset($this->properties[$name]);
					}
				}
			}
			$this->mode = 'r';
		}
		return $this;
	}
	
	/**
	 * Add required property name.
	 * 
	 * The property, corresponding to the given name added here, must be given during initialization.<br>
	 * <br>
	 * This method may only be called before initialization, with lazy-loading enabled 
	 * and only if the base access mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
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
	 * and only if the base access mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
	 * @param string[] $names
	 * <p>The names to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names): Properties
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard($this->lazy, [
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
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given property name is required.</p>
	 */
	final public function isRequiredPropertyName(string $name): bool
	{
		return $this->lazy ? isset($this->required_map[$name]) : $this->getProperty($name)->isRequired();
	}
	
	/**
	 * Add a new property with a given name.
	 * 
	 * This method may only be called before initialization and with lazy-loading disabled.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to add with.</p>
	 * @return \Feralygon\Kit\Managers\Properties\Property
	 * <p>The newly added property instance with the given name.</p>
	 */
	final public function addProperty(string $name): Property
	{
		//guard
		UCall::guard(!$this->lazy, [
			'hint_message' => "In order to add new properties to manager with owner {{owner}}, " . 
				"please set and use a builder function instead, as lazy-loading is enabled in this manager.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->initialized, [
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
	 * @since 1.0.0
	 * @param callable $builder
	 * <p>The function to set to build a property instance with a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name): ?Feralygon\Kit\Managers\Properties\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build with.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Managers\Properties\Property|null</b></code><br>
	 * The built property instance with the given name or <code>null</code> if none was built.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder(callable $builder): Properties
	{
		UCall::guard($this->lazy, [
			'hint_message' => "A builder function cannot be set in manager with owner {{owner}}, " . 
				"as lazy-loading is disabled.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization, in manager with owner {{owner}}.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::assert('builder', $builder, function (string $name): ?Property {});
		$this->builder = \Closure::fromCallable($builder);
		return $this;
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
	 * @since 1.0.0
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
		UCall::guard(!$this->initialized, [
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
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Interfaces\Propertiesable $object
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
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized(): bool
	{
		return $this->initialized;
	}
	
	/**
	 * Check if is initializing.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is initializing.</p>
	 */
	final public function isInitializing(): bool
	{
		return $this->initializing;
	}
	
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties to initialize with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param array|null $remainder [reference output] [default = null]
	 * <p>The properties remainder, which, if set, is gracefully filled with all remaining properties which have 
	 * not been found from the given <var>$properties</var> above, as <samp>name => value</samp> pairs or 
	 * an array of required property values or both.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(array $properties = [], ?array &$remainder = null): Properties
	{
		//guard
		UCall::guard(!$this->initialized, [
			'error_message' => "Manager with owner {{owner}} already initialized.",
			'parameters' => ['owner' => $this->owner]
		]);
		UCall::guard(!$this->lazy || isset($this->builder), [
			'error_message' => "No builder function set in manager with owner {{owner}}.",
			'hint_message' => "A builder function is required to be set, as lazy-loading is enabled.",
			'parameters' => ['owner' => $this->owner]
		]);
		
		//initialize remainder
		if (isset($remainder) || isset($this->remainderer)) {
			$remainder = [];
		}
		
		//initialize
		$this->initializing = true;
		try {
			//required (initialize)
			$required_map = [];
			if ($this->lazy) {
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
				$property_mode = $property->getMode();
				
				//guard
				UCall::guardParameter('properties', $properties, $property_mode !== 'r', [
					'error_message' => "Cannot set read-only property {{name}} in manager with owner {{owner}}.",
					'parameters' => ['name' => $name, 'owner' => $this->owner]
				]);
				
				//set
				UCall::guardParameter('properties', $properties, $property->setValue($value, true), [
					'error_message' => "Invalid value {{value}} for property {{name}} in manager with owner {{owner}}.",
					'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
				]);
				
				//write-once optimization
				if ($this->lazy && $property_mode === 'w-') {
					unset($this->properties[$name]);
				}
			}
			
			//properties (initialize)
			if (!$this->lazy) {
				foreach ($this->properties as $property) {
					if (!$property->isInitialized()) {
						$property->initialize();
					}
				}
			}
			
		} finally {
			$this->initializing = false;
		}
		$this->initialized = true;
		
		//return
		return $this;
	}
	
	/**
	 * Check if has property with a given name.
	 * 
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return mixed
	 * <p>The property value with the given name.</p>
	 */
	final public function get(string $name)
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
		
		//return
		return $property->getValue();
	}
	
	/**
	 * Get boolean property value with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @since 1.0.0
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
			'error_message' => "Invalid boolean value {{value}} in property {{name}} in manager with owner {{owner}}.",
			'hint_message' => "Only a boolean property is allowed be returned with this method.",
			'parameters' => ['name' => $name, 'value' => $value, 'owner' => $this->owner]
		]);
		
		//return
		return $value;
	}
	
	/**
	 * Check if property with a given name is set.
	 * 
	 * @since 1.0.0
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
	 * Set property with a given name and value.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value): Properties
	{
		//fallback
		if (isset($this->fallback_object) && !$this->hasProperty($name)) {
			$this->fallback_object->set($name, $value);
			return $this;
		}
		
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		
		//guard
		UCall::guard($property_mode !== 'r' && $property_mode !== 'r+', [
			'error_message' => "Cannot set read-only property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'w-', [
			'error_message' => "Cannot set write-once property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//set
		UCall::guardParameter('value', $value, $property->setValue($value, true), [
			'error_message' => "Invalid value for property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//return
		return $this;
	}
	
	/**
	 * Unset property with a given name.
	 * 
	 * @since 1.0.0
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
		UCall::guard($property_mode !== 'r' && $property_mode !== 'r+', [
			'error_message' => "Cannot unset read-only property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard($property_mode !== 'w-', [
			'error_message' => "Cannot unset write-once property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		UCall::guard(!$property->isRequired(), [
			'error_message' => "Cannot unset required property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//unset
		UCall::guard($property->resetValue(true), [
			'error_message' => "Cannot unset property {{name}} in manager with owner {{owner}}.",
			'parameters' => ['name' => $name, 'owner' => $this->owner]
		]);
		
		//return
		return $this;
	}
	
	/**
	 * Get all properties.
	 * 
	 * If lazy-loading is enabled, then only the currently loaded properties are returned.<br>
	 * Only properties which allow read access are returned.
	 * 
	 * @since 1.0.0
	 * @return array
	 * <p>All the properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll(): array
	{
		//properties
		$properties = [];
		foreach ($this->properties as $name => $property) {
			if ($property->getMode()[0] === 'r') {
				$properties[$name] = $property->getValue();
			}
		}
		
		//fallback
		if (isset($this->fallback_object)) {
			$properties += $this->fallback_object->getAll();
		}
		
		//return
		return $properties;
	}
	
	
	
	//Final protected methods
	/**
	 * Check if has property with a given name.
	 * 
	 * @since 1.0.0
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
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyNotFound
	 * @return \Feralygon\Kit\Managers\Properties\Property|null
	 * <p>The property instance with the given name.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, then <code>null</code> is returned if it was not found.</p>
	 */
	final protected function getProperty(string $name, bool $no_throw = false): ?Property
	{
		if (!isset($this->properties[$name])) {
			//build
			$property = null;
			if (isset($this->builder)) {
				$property = ($this->builder)($name);
				if ($this->initialized && isset($property) && !$property->isInitialized()) {
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
}
