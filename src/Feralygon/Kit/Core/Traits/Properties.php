<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

use Feralygon\Kit\Core\Traits\Properties\{
	Objects,
	Exceptions
};
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core properties trait.
 * 
 * This trait enables the support for a separate layer of custom properties in a class.<br>
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.<br>
 * <br>
 * They may also be set as read-only, so that all properties can still be retrieved, but not modified in any given way, 
 * or write-only, so that all properties can be modified, but not retrieved in any given way.
 * 
 * @since 1.0.0
 */
trait Properties
{
	//Private properties
	/** @var \Feralygon\Kit\Core\Traits\Properties\Objects\Property[] */
	private $properties = [];
	
	/** @var bool */
	private $properties_initialized = false;
	
	/** @var string[] */
	private $properties_required = [];
	
	/** @var string */
	private $properties_mode = 'rw';
	
	
	
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
			$this->get($name);
		} catch (Exceptions\PropertyNotFound $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Get property value from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotGetWriteonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyNotFound
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		if ($this->properties_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['object' => $this, 'name' => $name]);
		} elseif (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (!isset($this->properties[$name])) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		}
		return $this->properties[$name]->value;
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\InvalidBooleanPropertyValue
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		//check
		if ($this->properties_mode === 'r') {
			throw new Exceptions\CannotSetReadonlyProperty(['object' => $this, 'name' => $name]);
		} elseif (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (!isset($this->properties[$name])) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		}
		
		//set
		$property = $this->properties[$name];
		if (isset($property->evaluator)) {
			$v = $value;
			if (!($property->evaluator)($v)) {
				throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
			}
			$property->value = $v;
		} else {
			$property->value = $value;
		}
		
		//return
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotUnsetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		if ($this->properties_mode === 'r') {
			throw new Exceptions\CannotUnsetReadonlyProperty(['object' => $this, 'name' => $name]);
		} elseif (in_array($name, $this->properties_required, true)) {
			throw new Exceptions\CannotUnsetRequiredProperty(['object' => $this, 'name' => $name]);
		} elseif (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (isset($this->properties[$name])) {
			$property = $this->properties[$name];
			$property->value = $property->default_value;
		}
		return $this;
	}
	
	/**
	 * Get properties.
	 * 
	 * @since 1.0.0
	 * @return array <p>The properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getProperties() : array
	{
		$properties = [];
		if ($this->properties_mode[0] === 'r') {
			foreach ($this->properties as $name => $property) {
				$properties[$name] = $property->value;
			}
		}
		return $properties;
	}
	
	
	
	//Final protected methods
	/**
	 * Add required property names.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string[] $names <p>The property names to add.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @return void
	 */
	final protected function addRequiredPropertyNames(array $names) : void
	{
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		}
		$this->properties_required = array_merge($this->properties_required, $names);
	}
	
	/**
	 * Add property with a given name.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param callable|null $evaluator [default = null] <p>The function to evaluate a given property value.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (&$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The property value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given property value is successfully evaluated.
	 * </p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyAlreadyAdded
	 * @return void
	 */
	final protected function addProperty(string $name, ?callable $evaluator = null, bool $override = false) : void
	{
		//check
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		} elseif (isset($this->properties[$name]) && !$override) {
			throw new Exceptions\PropertyAlreadyAdded(['object' => $this, 'name' => $name]);
		}
		
		//evaluator
		if (isset($evaluator)) {
			UCall::assertSignature('evaluator', $evaluator, function (&$value) : bool {}, true);
			$evaluator = \Closure::fromCallable($evaluator);
		}
		
		//add
		$this->properties[$name] = new Objects\Property($evaluator);
	}
	
	/**
	 * Set property default value.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property default value to set.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\InvalidPropertyValue
	 * @return void
	 */
	final protected function setPropertyDefaultValue(string $name, $value) : void
	{
		//check
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		} elseif (!isset($this->properties[$name])) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		}
		
		//set
		$property = $this->properties[$name];
		if (isset($property->evaluator)) {
			$v = $value;
			if (!($property->evaluator)($v)) {
				throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
			}
			$property->default_value = $v;
		} else {
			$property->default_value = $value;
		}
		$property->value = $property->default_value;
	}
	
	
	
	//Final private methods
	/**
	 * Initialize a given set of properties with a given loader function.
	 * 
	 * @since 1.0.0
	 * @param array $properties <p>The properties to initialize, as <samp>name => value</samp> pairs.</p>
	 * @param callable $loader <p>The function to load all properties.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function () : void</code><br>
	 * <br>
	 * Return: <code><b>void</b></code>
	 * </p>
	 * @param string $mode [default = 'rw'] <p>The properties read and write mode to initialize with, 
	 * which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow properties to be both read from and written to.<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow properties to be only read from.<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow properties to be only written to.
	 * </p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\InvalidPropertiesMode
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\MissingRequiredProperties
	 * @return void
	 */
	final private function initializeProperties(array $properties, callable $loader, string $mode = 'rw') : void
	{
		//check
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		} elseif (!in_array($mode, $this->getPropertyModes(), true)) {
			throw new Exceptions\InvalidPropertiesMode([
				'object' => $this,
				'mode' => $mode,
				'modes' => $this->getPropertyModes()
			]);
		}
		
		//load
		UCall::assertSignature('loader', $loader, function () : void {}, true);
		$loader();
		
		//required
		if (!empty($this->properties_required)) {
			$missing = array_keys(array_diff_key(array_flip($this->properties_required), $properties));
			if (!empty($missing)) {
				throw new Exceptions\MissingRequiredProperties(['object' => $this, 'names' => $missing]);
			}
		}
		
		//initialize
		$this->properties_initialized = true;
		
		//properties
		foreach ($properties as $name => $value) {
			$this->set($name, $value);
		}
		
		//mode
		$this->properties_mode = $mode;
	}
	
	
	
	//Final private static methods
	/**
	 * Get property modes.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The property modes.</p>
	 */
	final private static function getPropertyModes() : array
	{
		return ['rw', 'r', 'w'];
	}
}
