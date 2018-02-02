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
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Data as UData,
	Type as UType
};

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
	
	/** @var bool[] */
	private $properties_required_map = [];
	
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotGetWriteonlyProperty
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		if (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (!isset($this->properties[$name])) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		} elseif ($this->properties_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['object' => $this, 'name' => $name]);
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		//check
		if (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (!isset($this->properties[$name])) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		} elseif ($this->properties_mode === 'r') {
			throw new Exceptions\CannotSetReadonlyProperty(['object' => $this, 'name' => $name]);
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\CannotUnsetReadonlyProperty
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		if (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		} elseif (isset($this->properties_required_map[$name])) {
			throw new Exceptions\CannotUnsetRequiredProperty(['object' => $this, 'name' => $name]);
		} elseif ($this->properties_mode === 'r') {
			throw new Exceptions\CannotUnsetReadonlyProperty(['object' => $this, 'name' => $name]);
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
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertyAlreadyAdded
	 * @return void
	 */
	final protected function addProperty(
		string $name, ?callable $evaluator = null, bool $required = false, bool $override = false
	) : void
	{
		//check
		if ($this->properties_initialized) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		} elseif (isset($this->properties[$name]) && !$override) {
			throw new Exceptions\PropertyAlreadyAdded(['object' => $this, 'name' => $name]);
		} elseif ($override && !$required) {
			unset($this->properties_required_map[$name]);
		}
		
		//evaluator
		if (isset($evaluator)) {
			UCall::assertSignature('evaluator', $evaluator, function (&$value) : bool {}, true);
			$evaluator = \Closure::fromCallable($evaluator);
		}
		
		//add
		$this->properties[$name] = new Objects\Property($evaluator);
		if ($required) {
			$this->properties_required_map[$name] = true;
		}
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a boolean.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only the following types and formats can be evaluated into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean, as: <code>false</code> for boolean <code>false</code>, 
	 * and <code>true</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addBooleanProperty(
		string $name, bool $required = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($nullable) : bool {
			return UType::evaluateBoolean($value, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a number.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only the following types and formats can be evaluated into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addNumberProperty(
		string $name, bool $required = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($nullable) : bool {
			return UType::evaluateNumber($value, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as an integer.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only the following types and formats can be evaluated into an integer:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addIntegerProperty(
		string $name, bool $required = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($nullable) : bool {
			return UType::evaluateInteger($value, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a float.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only the following types and formats can be evaluated into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123.45kB"</code> or <code>"123.45 kilobytes"</code> for <code>123450.0</code>.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addFloatProperty(
		string $name, bool $required = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($nullable) : bool {
			return UType::evaluateFloat($value, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a string.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only a string, integer or float can be evaluated into a string.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param bool $non_empty [default = false] <p>Do not allow an empty string property value.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addStringProperty(
		string $name, bool $required = false, bool $non_empty = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($non_empty, $nullable) : bool {
			return UType::evaluateString($value, $non_empty, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a class.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a property value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addClassProperty(
		string $name, bool $required = false, $base_object_class = null, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($base_object_class, $nullable) : bool {
			return UType::evaluateClass($value, $base_object_class, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as an object.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only a class string or object can be evaluated into an object.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a property value must be or extend from.</p>
	 * @param array $arguments [default = []] <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addObjectProperty(
		string $name, bool $required = false, $base_object_class = null, array $arguments = [], bool $nullable = false, 
		bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($base_object_class, $arguments, $nullable) : bool {
			return UType::evaluateObject($value, $base_object_class, $arguments, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a class or object.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.<br>
	 * <br>
	 * Only a class string or object can be evaluated into an object or class.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class 
	 * which a property value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addObjectClassProperty(
		string $name, bool $required = false, $base_object_class = null, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($base_object_class, $nullable) : bool {
			return UType::evaluateObjectClass($value, $base_object_class, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as a callable.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param callable|null $template [default = null] <p>The template callable declaration 
	 * to validate the signature against.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $assertive [default = false] <p>Evaluate in an assertive manner, in other words, 
	 * perform the heavier validations, such as the template one, only when in a debug environment.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addCallableProperty(
		string $name, bool $required = false, ?callable $template = null, bool $nullable = false, 
		bool $assertive = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($template, $nullable, $assertive) : bool {
			return UCall::evaluate($value, $template, $nullable, $assertive);
		}, $required, $override);
	}
	
	/**
	 * Add property with a given name, which only allows a value evaluated as an array.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @param bool $required [default = false] <p>Set property as required to be given during instantiation.</p>
	 * @param callable|null $evaluator [default = null] <p>The evaluator function to use for each element 
	 * in the resulting array value.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (&$key, &$value) : bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>int|string $key</b> [reference]</code> : 
	 * The array element key to evaluate (validate and sanitize).<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The array element value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool</b></code><br>
	 * Boolean <code>true</code> if the given array element is successfully evaluated.
	 * </p>
	 * @param bool $non_associative [default = false] <p>Do not allow an associative array property value.</p>
	 * @param bool $non_empty [default = false] <p>Do not allow an empty array property value.</p>
	 * @param bool $nullable [default = false] <p>Allow a property value to evaluate as <code>null</code>.</p>
	 * @param bool $override [default = false] <p>Override any existing property with the same name.</p>
	 * @return void
	 */
	final protected function addArrayProperty(
		string $name, bool $required = false, ?callable $evaluator = null, bool $non_associative = false, 
		bool $non_empty = false, bool $nullable = false, bool $override = false
	) : void
	{
		$this->addProperty($name, function (&$value) use ($evaluator, $non_associative, $non_empty, $nullable) : bool {
			return UData::evaluate($value, $evaluator, $non_associative, $non_empty, $nullable);
		}, $required, $override);
	}
	
	/**
	 * Set property with a given name with a given default value.
	 * 
	 * This method can only be called during the properties initialization, namely from the loader function set.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property default value to set with.</p>
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
		if (!empty($this->properties_required_map)) {
			$missing = array_keys(array_diff_key($this->properties_required_map, $properties));
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
