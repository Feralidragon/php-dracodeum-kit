<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers;

use Feralygon\Kit\Managers\Properties\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions as PropertyExceptions;
use Feralygon\Kit\Utilities\Call as UCall;

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
 * @see \Feralygon\Kit\Managers\Properties\Objects\Property
 */
class Properties
{
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
	
	/** @var \Feralygon\Kit\Managers\Properties\Objects\Property[] */
	private $properties = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner
	 * <p>The owner object.</p>
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
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\InvalidMode
	 */
	final public function __construct(object $owner, bool $lazy = false, string $mode = 'rw')
	{
		//initialize
		$this->owner = $owner;
		$this->lazy = $lazy;
		
		//mode
		if (!in_array($mode, self::MODES, true)) {
			throw new Exceptions\InvalidMode(['manager' => $this, 'mode' => $mode, 'modes' => self::MODES]);
		}
		$this->mode = $mode;
	}
	
	
	
	//Public methods
	/**
	 * Create a property instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to create with.</p>
	 * @return \Feralygon\Kit\Managers\Properties\Objects\Property
	 * <p>The created property instance with the given name.</p>
	 */
	public function createProperty(string $name) : Objects\Property
	{
		return new Objects\Property($this, $name);
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @since 1.0.0
	 * @return object
	 * <p>The owner object.</p>
	 */
	final public function getOwner() : object
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
	final public function isLazy() : bool
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
	final public function getMode() : string
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
	final public function isReadonly() : bool
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
	final public function setAsReadonly() : Properties
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
	 * Add a given set of required property names.
	 * 
	 * The properties, corresponding to the given required property names added here, 
	 * must be given during initialization.<br>
	 * <br>
	 * This method may only be called before initialization, with lazy-loading enabled 
	 * and only if the base access mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
	 * @param string[] $names
	 * <p>The required property names to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names) : Properties
	{
		//guard
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		UCall::guard($this->lazy, [
			'hint_message' => "In order to explicitly set a property as required, with lazy-loading disabled, "  . 
				"please use the \"setAsRequired\" method instead from the corresponding property instance."
		]);
		UCall::guard($this->mode !== 'r', [
			'hint_message' => "Required property names cannot be set as all properties are strictly read-only."
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
	 * <p>The property name to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given property name is required.</p>
	 */
	final public function isRequiredPropertyName(string $name) : bool
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
	 * <p>The property name to add.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyAlreadyAdded
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyNameMismatch
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyManagerMismatch
	 * @return \Feralygon\Kit\Managers\Properties\Objects\Property
	 * <p>The newly added property instance with the given name.</p>
	 */
	final public function addProperty(string $name) : Objects\Property
	{
		//guard
		UCall::guard(!$this->lazy, [
			'hint_message' => "In order to add new properties, with lazy-loading enabled, " . 
				"please set and use a builder function instead."
		]);
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		
		//check
		if (isset($this->properties[$name])) {
			throw new Exceptions\PropertyAlreadyAdded(['manager' => $this, 'name' => $name]);
		}
		
		//property
		$property = $this->createProperty($name);
		if ($property->getName() !== $name) {
			throw new Exceptions\PropertyNameMismatch([
				'manager' => $this, 'name' => $name, 'property' => $property
			]);
		} elseif ($property->getManager() !== $this) {
			throw new Exceptions\PropertyManagerMismatch(['manager' => $this, 'property' => $property]);
		}
		$this->properties[$name] = $property;
		
		//return
		return $property;
	}
	
	/**
	 * Set builder function.
	 * 
	 * A builder function is required to be set when lazy-loading is enabled.<br>
	 * This method may only be called before initialization and with lazy-loading enabled.
	 * 
	 * @since 1.0.0
	 * @param callable $builder
	 * <p>The function to build a property instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name) : ?\Feralygon\Kit\Managers\Properties\Objects\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property name to build for.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Managers\Properties\Objects\Property|null</b></code><br>
	 * The built property instance for the given name or <code>null</code> if none was built.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder(callable $builder) : Properties
	{
		UCall::guard($this->lazy, [
			'hint_message' => "A builder function is only required when lazy-loading is enabled."
		]);
		UCall::guard(!$this->initialized, [
			'hint_message' => "This method may only be called before initialization."
		]);
		UCall::assert('builder', $builder, function (string $name) : ?Objects\Property {}, true);
		$this->builder = \Closure::fromCallable($builder);
		return $this;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
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
	final public function isInitializing() : bool
	{
		return $this->initializing;
	}
	
	/**
	 * Initialize.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties to initialize with, as <samp>name => value</samp> pairs.</p>
	 * @param array|null $remaining [reference output] [default = null]
	 * <p>If set, it is gracefully filled with all properties, from the given <var>$properties</var> above, 
	 * which have not been found, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\AlreadyInitialized
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\NoBuilderSet
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\MissingRequiredProperties
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\InvalidPropertyValue
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(array $properties = [], ?array &$remaining = null) : Properties
	{
		//pre-initialize
		if (isset($remaining)) {
			$remaining = [];
		}
		
		//validate
		if ($this->initialized) {
			throw new Exceptions\AlreadyInitialized(['manager' => $this]);
		} elseif ($this->lazy && !isset($this->builder)) {
			throw new Exceptions\NoBuilderSet(['manager' => $this]);
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
			
			//required (process)
			if (!empty($required_map)) {
				$missing_names = array_keys(array_diff_key($required_map, $properties));
				if (!empty($missing_names)) {
					throw new Exceptions\MissingRequiredProperties(['manager' => $this, 'names' => $missing_names]);
				}
			}
			
			//properties (set value)
			foreach ($properties as $name => $value) {
				//remaining
				if (isset($remaining) && !$this->hasProperty($name)) {
					$remaining[$name] = $value;
					continue;
				}
				
				//property
				$property = $this->getProperty($name);
				if ($property->getMode() === 'r') {
					throw new Exceptions\CannotSetReadonlyProperty(['manager' => $this, 'property' => $property]);
				}
				
				//set value
				try {
					$property->setValue($value);
				} catch (PropertyExceptions\InvalidValue $exception) {
					throw new Exceptions\InvalidPropertyValue([
						'manager' => $this, 'property' => $property, 'value' => $value
					]);
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
	 * <p>The property name to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name) : bool
	{
		return $this->hasProperty($name);
	}
	
	/**
	 * Get property value from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotGetWriteonlyProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotGetWriteonceProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyNoDefaultValueSet
	 * @return mixed
	 * <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['manager' => $this, 'property' => $property]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotGetWriteonceProperty(['manager' => $this, 'property' => $property]);
		}
		
		//get
		try {
			return $property->getValue();
		} catch (PropertyExceptions\NoDefaultValueSet $exception) {
			throw new Exceptions\PropertyNoDefaultValueSet(['manager' => $this, 'property' => $property]);
		}
		return null;
	}
	
	/**
	 * Get boolean property value from a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\InvalidBooleanPropertyValue
	 * @return bool
	 * <p>The boolean property value from the given name.</p>
	 */
	final public function is(string $name) : bool
	{
		$value = $this->get($name);
		if (!is_bool($value)) {
			throw new Exceptions\InvalidBooleanPropertyValue([
				'manager' => $this, 'property' => $this->getProperty($name), 'value' => $value
			]);
		}
		return $value;
	}
	
	/**
	 * Check if property is set for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if property is set for the given name.</p>
	 */
	final public function isset(string $name) : bool
	{
		return $this->has($name) ? $this->get($name) !== null : false;
	}
	
	/**
	 * Set property with a given name with a given value.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to set for.</p>
	 * @param mixed $value
	 * <p>The property value to set with.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotSetWriteonceProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\InvalidPropertyValue
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value) : Properties
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r' || $property_mode === 'r+') {
			throw new Exceptions\CannotSetReadonlyProperty(['manager' => $this, 'property' => $property]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotSetWriteonceProperty(['manager' => $this, 'property' => $property]);
		}
		
		//set
		try {
			$property->setValue($value);
		} catch (PropertyExceptions\InvalidValue $exception) {
			throw new Exceptions\InvalidPropertyValue(['manager' => $this, 'property' => $property, 'value' => $value]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotUnsetReadonlyProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotUnsetWriteonceProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\CannotUnsetProperty
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name) : Properties
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r' || $property_mode === 'r+') {
			throw new Exceptions\CannotUnsetReadonlyProperty(['manager' => $this, 'property' => $property]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotUnsetWriteonceProperty(['manager' => $this, 'property' => $property]);
		} elseif ($property->isRequired()) {
			throw new Exceptions\CannotUnsetRequiredProperty(['manager' => $this, 'property' => $property]);
		}
		
		//unset
		try {
			$property->resetValue();
		} catch (PropertyExceptions\NoDefaultValueSet $exception) {
			throw new Exceptions\CannotUnsetProperty(['manager' => $this, 'property' => $property]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Get all properties.
	 * 
	 * If lazy-loading is enabled, only the currently loaded properties are returned.<br>
	 * Only properties which allow read access are returned.
	 * 
	 * @since 1.0.0
	 * @return array
	 * <p>All properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll() : array
	{
		$properties = [];
		foreach ($this->properties as $name => $property) {
			if ($property->getMode()[0] === 'r') {
				$properties[$name] = $property->getValue();
			}
		}
		return $properties;
	}
	
	
	
	//Final protected methods
	/**
	 * Check if has property with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to check for.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyNameMismatch
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyManagerMismatch
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final protected function hasProperty(string $name) : bool
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
			
			//check
			if (!isset($property)) {
				return false;
			} elseif ($property->getName() !== $name) {
				throw new Exceptions\PropertyNameMismatch([
					'manager' => $this, 'name' => $name, 'property' => $property
				]);
			} elseif ($property->getManager() !== $this) {
				throw new Exceptions\PropertyManagerMismatch(['manager' => $this, 'property' => $property]);
			}
			
			//set
			$this->properties[$name] = $property;
		}
		return true;
	}
	
	/**
	 * Get property instance from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Managers\Properties\Exceptions\PropertyNotFound
	 * @return \Feralygon\Kit\Managers\Properties\Objects\Property
	 * <p>The property instance from the given name.</p>
	 */
	final protected function getProperty(string $name) : Objects\Property
	{
		if (!$this->hasProperty($name)) {
			throw new Exceptions\PropertyNotFound(['manager' => $this, 'name' => $name]);
		}
		return $this->properties[$name];
	}
}
