<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers;

use Feralygon\Kit\Core\Managers\Properties\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exceptions as PropertyExceptions;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core properties manager class.
 * 
 * This manager handles and stores a separate set of properties.
 * 
 * //TODO: more complete description
 * 
 * @since 1.0.0
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
	
	/** @var bool[] */
	private $required_map = [];
	
	/** @var \Closure|null */
	private $builder = null;
	
	/** @var \Feralygon\Kit\Core\Managers\Properties\Objects\Property[] */
	private $properties = [];
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param object $owner <p>The owner object.</p>
	 * @param bool $lazy [default = false] <p>Use lazy-loading, so that each property is only loaded on access.<br>
	 * <br>
	 * NOTE: With lazy-loading, the existence of each property becomes unknown ahead of time, 
	 * therefore when retrieving a list of all properties, only a list of the currently loaded ones is returned.</p>
	 * @param string $mode [default = 'rw'] <p>The read and write mode to set for all properties, 
	 * which, if set, must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow all properties to be only strictly read from, 
	 * so that they cannot be given during initialization (strict read-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>r+</samp> : Allow all properties to be only read from (read-only), 
	 * although they may still be given during initialization.<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow all properties to be both read from 
	 * and written to (read-write).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow all properties to be only written to (write-only).<br>
	 * &nbsp; &#8226; &nbsp; <samp>w-</samp> : Allow all properties to be only written to, 
	 * but only once during initialization (write-once).
	 * </p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidOwner
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidMode
	 */
	final public function __construct($owner, bool $lazy = false, string $mode = 'rw')
	{
		//owner
		if (!is_object($owner)) {
			throw new Exceptions\InvalidOwner(['manager' => $this, 'owner' => $owner]);
		}
		$this->owner = $owner;
		
		//lazy
		$this->lazy = $lazy;
		
		//mode
		if (!in_array($mode, self::MODES, true)) {
			throw new Exceptions\InvalidMode(['manager' => $this, 'mode' => $mode, 'modes' => self::MODES]);
		}
		$this->mode = $mode;
	}
	
	
	
	//Public methods
	/**
	 * Create a property instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Core\Managers\Properties\Objects\Property <p>The created property instance.</p>
	 */
	public function createProperty() : Objects\Property
	{
		return new Objects\Property($this);
	}
	
	
	
	//Final public methods
	/**
	 * Get owner object.
	 * 
	 * @since 1.0.0
	 * @return object <p>The owner object.</p>
	 */
	final public function getOwner()
	{
		return $this->owner;
	}
	
	/**
	 * Check if lazy-loading is enabled.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if lazy-loading is enabled.</p>
	 */
	final public function isLazy() : bool
	{
		return $this->lazy;
	}
	
	/**
	 * Get mode.
	 * 
	 * @since 1.0.0
	 * @return string <p>The mode.</p>
	 */
	final public function getMode() : string
	{
		return $this->mode;
	}
	
	/**
	 * Add a given set of required property names.
	 * 
	 * The properties, corresponding to the given required property names added here, 
	 * must be given during initialization.<br>
	 * <br>
	 * This method is only allowed to be called before initialization, with lazy-loading enabled 
	 * and only if the global mode is not set to strict read-only.
	 * 
	 * @since 1.0.0
	 * @param string[] $names <p>The required property names to add.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\MethodCallNotAllowed
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function addRequiredPropertyNames(array $names) : Properties
	{
		if ($this->initialized) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'hint_message' => "This method may only be called before initialization."
			]);
		} elseif (!$this->lazy) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'lazy' => $this->lazy,
				'hint_message' => "In order to explicitly set a property as required, "  . 
					"please use the \"setAsRequired\" method instead from the corresponding property object."
			]);
		} elseif ($this->mode === 'r') {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'hint_message' => "Required property names cannot be set as all properties are strictly read-only."
			]);
		}
		$this->required_map += array_fill_keys($names, true);
		return $this;
	}
	
	/**
	 * Add a new property with a given name.
	 * 
	 * This method is only allowed to be called before initialization and with lazy-loading disabled.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\MethodCallNotAllowed
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\PropertyAlreadyAdded
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\PropertyManagerMismatch
	 * @return \Feralygon\Kit\Core\Managers\Properties\Objects\Property 
	 * <p>The resulting newly added property instance with the given name.</p>
	 */
	final public function addProperty(string $name) : Objects\Property
	{
		//validate
		if ($this->initialized) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'hint_message' => "This method may only be called before initialization."
			]);
		} elseif ($this->lazy) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'lazy' => $this->lazy,
				'hint_message' => "In order to add new properties, please set and use a builder function instead."
			]);
		} elseif (isset($this->properties[$name])) {
			throw new Exceptions\PropertyAlreadyAdded(['manager' => $this, 'name' => $name]);
		}
		
		//property
		$property = $this->createProperty();
		if ($property->getManager() !== $this) {
			throw new Exceptions\PropertyManagerMismatch([
				'manager' => $this, 'name' => $name, 'property_manager' => $property->getManager()
			]);
		}
		$this->properties[$name] = $property;
		
		//return
		return $this;
	}
	
	/**
	 * Set builder function.
	 * 
	 * A builder function is required to be set when lazy-loading is enabled.<br>
	 * This method is only allowed to be called before initialization and with lazy-loading enabled.
	 * 
	 * @since 1.0.0
	 * @param callable $builder <p>The function to build a property instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name) : ?\Feralygon\Kit\Core\Managers\Properties\Objects\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code> : The property name to build for.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Core\Managers\Properties\Objects\Property|null</b></code><br>
	 * The built property instance for the given name or <code>null</code> if none was built.
	 * </p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\MethodCallNotAllowed
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function setBuilder(callable $builder) : Properties
	{
		if ($this->initialized) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'hint_message' => "This method may only be called before initialization."
			]);
		} elseif (!$this->lazy) {
			throw new Exceptions\MethodCallNotAllowed([
				'manager' => $this,
				'lazy' => $this->lazy,
				'hint_message' => "A builder function is only required when lazy-loading is enabled."
			]);
		}
		UCall::assert('builder', $builder, function (string $name) : ?Objects\Property {}, true);
		$this->builder = \Closure::fromCallable($builder);
		return $this;
	}
	
	/**
	 * Check if is initialized.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if is initialized.</p>
	 */
	final public function isInitialized() : bool
	{
		return $this->initialized;
	}
	
	/**
	 * Initialize with a given set of properties.
	 * 
	 * @since 1.0.0
	 * @param array $properties <p>The properties to initialize with, as <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\AlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\NoBuilderSet
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\MissingRequiredProperties
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function initialize(array $properties) : Properties
	{
		//validate
		if ($this->initialized) {
			throw new Exceptions\AlreadyInitialized(['manager' => $this]);
		} elseif ($this->lazy && !isset($this->builder)) {
			throw new Exceptions\NoBuilderSet(['manager' => $this]);
		}
		
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
			$missing = array_keys(array_diff_key($required_map, $properties));
			if (!empty($missing)) {
				throw new Exceptions\MissingRequiredProperties(['manager' => $this, 'names' => $missing]);
			}
		}
		
		//initialized
		$this->initialized = true;
		
		//properties
		foreach ($properties as $name => $value) {
			try {
				$property = $this->getProperty($name);
				if ($property->getMode() === 'r') {
					throw new Exceptions\CannotSetReadonlyProperty(['manager' => $this, 'name' => $name]);
				}
				$property->setValue($value);
			} catch (PropertyExceptions\InvalidValue $exception) {
				throw new Exceptions\InvalidPropertyValue(['manager' => $this, 'name' => $name, 'value' => $value]);
			}
		}
		
		//return
		return $this;
	}
	
	/**
	 * Check if has property with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name) : bool
	{
		try {
			$this->getProperty($name);
		} catch (Exceptions\PropertyNotFound $exception) {
			return false;
		} catch (Exceptions\PropertyNotInitialized $exception) {}
		return true;
	}
	
	/**
	 * Get property value from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotGetWriteonlyProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotGetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\PropertyNotInitialized
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['manager' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotGetWriteonceProperty(['manager' => $this, 'name' => $name]);
		}
		
		//get
		$value = null;
		try {
			$value = $property->getValue();
		} catch (PropertyExceptions\NotInitialized $exception) {
			throw new Exceptions\PropertyNotInitialized(['manager' => $this, 'name' => $name]);
		}
		return $value;
	}
	
	/**
	 * Get boolean property value from a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidBooleanPropertyValue
	 * @return bool <p>The boolean property value from the given name.</p>
	 */
	final public function is(string $name) : bool
	{
		$value = $this->get($name);
		if (!is_bool($value)) {
			throw new Exceptions\InvalidBooleanPropertyValue(['manager' => $this, 'name' => $name, 'value' => $value]);
		}
		return $value;
	}
	
	/**
	 * Check if property is set for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if property is set for the given name.</p>
	 */
	final public function isset(string $name) : bool
	{
		return $this->has($name) ? $this->get($name) !== null : false;
	}
	
	/**
	 * Set property with a given name with a given value.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property value to set with.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotSetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r' || $property_mode === 'r+') {
			throw new Exceptions\CannotSetReadonlyProperty(['manager' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotSetWriteonceProperty(['manager' => $this, 'name' => $name]);
		}
		
		//set
		try {
			$property->setValue($value);
		} catch (PropertyExceptions\InvalidValue $exception) {
			throw new Exceptions\InvalidPropertyValue(['manager' => $this, 'name' => $name, 'value' => $value]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotUnsetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotUnsetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\CannotUnsetProperty
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r' || $property_mode === 'r+') {
			throw new Exceptions\CannotUnsetReadonlyProperty(['manager' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotUnsetWriteonceProperty(['manager' => $this, 'name' => $name]);
		} elseif ((!$this->lazy && $property->isRequired()) || ($this->lazy && isset($this->required_map[$name]))) {
			throw new Exceptions\CannotUnsetRequiredProperty(['manager' => $this, 'name' => $name]);
		}
		
		//unset
		try {
			$property->resetValue();
		} catch (PropertyExceptions\NoDefaultValueSet $exception) {
			throw new Exceptions\CannotUnsetProperty(['manager' => $this, 'name' => $name]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Get all properties.
	 * 
	 * If lazy-loading is enabled, only the currently loaded properties are returned.
	 * 
	 * @since 1.0.0
	 * @return array <p>All the properties, as <samp>name => value</samp> pairs.</p>
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
	 * Get property instance from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\NotInitialized
	 * @throws \Feralygon\Kit\Core\Managers\Properties\Exceptions\PropertyNotFound
	 * @return \Feralygon\Kit\Core\Managers\Properties\Objects\Property 
	 * <p>The property instance from the given name.</p>
	 */
	final protected function getProperty(string $name) : Objects\Property
	{
		if (!$this->initialized) {
			throw new Exceptions\NotInitialized(['manager' => $this]);
		} elseif (!isset($this->properties[$name])) {
			$property = null;
			if (isset($this->builder)) {
				$property = ($this->builder)($name);
			}
			if (!isset($property)) {
				throw new Exceptions\PropertyNotFound(['manager' => $this, 'name' => $name]);
			}
			$this->properties[$name] = $property;
		}
		return $this->properties[$name];
	}
}
