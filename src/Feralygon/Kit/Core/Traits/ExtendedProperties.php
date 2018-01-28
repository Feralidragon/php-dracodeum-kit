<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

use Feralygon\Kit\Core\Traits\ExtendedProperties\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exceptions as PropertyExceptions;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core extended properties trait.
 * 
 * This trait enables the support for a separate layer of custom extended properties in a class.<br>
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.<br>
 * <br>
 * Each property may behave individually in a distinct manner, 
 * and may be read-write, read-only, write-only or even write-once.<br>
 * Write-once properties can only be set during instantiation, 
 * and cannot be retrieved nor modified afterwards, acting as initialization parameters only.<br>
 * <br>
 * Each property may also have a getter and setter function, 
 * to link the property directly towards functions or methods instead of holding its own value.
 * 
 * @since 1.0.0
 */
trait ExtendedProperties
{
	//Private properties
	/** @var \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property[] */
	private $properties = [];
	
	/** @var bool */
	private $properties_initialized = false;
	
	/** @var \Closure|null */
	private $properties_builder = null;
	
	/** @var string[] */
	private $properties_required = [];
	
	
	
	//Final public magic methods
	/**
	 * Get property value from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function __get(string $name)
	{
		return $this->get($name);
	}
	
	/**
	 * Check if property is set for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if property is set for the given name.</p>
	 */
	final public function __isset(string $name) : bool
	{
		return $this->isset($name);
	}
	
	/**
	 * Set property with a given name with a given value.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property value to set with.</p>
	 * @return void
	 */
	final public function __set(string $name, $value) : void
	{
		$this->set($name, $value);
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @return void
	 */
	final public function __unset(string $name) : void
	{
		$this->unset($name);
	}
	
	
	
	//Final public methods
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
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotGetWriteonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotGetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\PropertyNotInitialized
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['object' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotGetWriteonceProperty(['object' => $this, 'name' => $name]);
		}
		
		//get
		$value = null;
		try {
			$value = $property->getValue();
		} catch (PropertyExceptions\NotInitialized $exception) {
			throw new Exceptions\PropertyNotInitialized(['object' => $this, 'name' => $name]);
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
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\InvalidBooleanPropertyValue
	 * @return bool <p>The boolean property value from the given name.</p>
	 */
	final public function is(string $name) : bool
	{
		$value = $this->get($name);
		if (!is_bool($value)) {
			throw new Exceptions\InvalidBooleanPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
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
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotSetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r') {
			throw new Exceptions\CannotSetReadonlyProperty(['object' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotSetWriteonceProperty(['object' => $this, 'name' => $name]);
		}
		
		//set
		try {
			$property->setValue($value);
		} catch (PropertyExceptions\InvalidValue $exception) {
			throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotUnsetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotUnsetWriteonceProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\CannotUnsetProperty
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		//property
		$property = $this->getProperty($name);
		$property_mode = $property->getMode();
		if ($property_mode === 'r') {
			throw new Exceptions\CannotUnsetReadonlyProperty(['object' => $this, 'name' => $name]);
		} elseif ($property_mode === 'w-') {
			throw new Exceptions\CannotUnsetWriteonceProperty(['object' => $this, 'name' => $name]);
		} elseif (in_array($name, $this->properties_required, true)) {
			throw new Exceptions\CannotUnsetRequiredProperty(['object' => $this, 'name' => $name]);
		}
		
		//unset
		try {
			$property->resetValue();
		} catch (PropertyExceptions\NoDefaultValueSet $exception) {
			throw new Exceptions\CannotUnsetProperty(['object' => $this, 'name' => $name]);
		}
		
		//return
		return $this;
	}
	
	/**
	 * Get loaded properties.
	 * 
	 * @since 1.0.0
	 * @return array <p>The loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getLoadedProperties() : array
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
	 * Create a property instance.
	 * 
	 * @since 1.0.0
	 * @return \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property <p>The created property instance.</p>
	 */
	final protected function createProperty() : Objects\Property
	{
		return new Objects\Property();
	}
	
	
	
	//Final private methods
	/**
	 * Initialize a given set of properties with a given builder function.
	 * 
	 * @since 1.0.0
	 * @param array $properties <p>The properties to initialize, as <samp>name => value</samp> pairs.</p>
	 * @param callable $builder <p>The function to build a property instance for a given name.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (string $name) : ?\Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code> : The property name to build for.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property|null</b></code><br>
	 * The built property instance for the given name or <code>null</code> if none was built.
	 * </p>
	 * @param string[] $required [default = []] <p>The required property names.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\MissingRequiredProperties
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\InvalidPropertyValue
	 * @return void
	 */
	final private function initializeProperties(array $properties, callable $builder, array $required = []) : void
	{
		//check
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		}
		
		//required
		$this->properties_required = $required;
		if (!empty($required)) {
			$missing = array_keys(array_diff_key(array_flip($required), $properties));
			if (!empty($missing)) {
				throw new Exceptions\MissingRequiredProperties(['object' => $this, 'names' => $missing]);
			}
		}
		
		//builder
		UCall::assertSignature('builder', $builder, function (string $name) : ?Objects\Property {}, true);
		$this->properties_builder = \Closure::fromCallable($builder);
		
		//initialized
		$this->properties_initialized = true;
		
		//properties
		foreach ($properties as $name => $value) {
			try {
				$this->getProperty($name)->setValue($value);
			} catch (PropertyExceptions\InvalidValue $exception) {
				throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
			}
		}
	}
	
	/**
	 * Get property instance from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions\PropertyNotFound
	 * @return \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property 
	 * <p>The property instance from the given name.</p>
	 */
	final private function getProperty(string $name) : Objects\Property
	{
		if (!isset($this->properties[$name])) {
			//check
			if (!$this->properties_initialized) {
				throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
			}
			
			//property
			$property = ($this->properties_builder)($name);
			if (!isset($property)) {
				throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
			}
			$this->properties[$name] = $property;
		}
		return $this->properties[$name];
	}
}
