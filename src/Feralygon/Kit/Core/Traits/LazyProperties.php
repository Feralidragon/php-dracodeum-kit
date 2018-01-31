<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

use Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core lazy properties trait.
 * 
 * This trait enables the support for a separate layer of custom lazy-loaded properties in a class.<br>
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.<br>
 * <br>
 * Each and every property is only loaded on demand (lazy-loading).<br>
 * <br>
 * They may also be set as read-only, so that all properties can still be retrieved, but not modified in any given way, 
 * or write-only, so that all properties can be modified, but not retrieved in any given way.
 * 
 * @since 1.0.0
 */
trait LazyProperties
{
	//Private properties
	/** @var array */
	private $properties = [];
	
	/** @var bool */
	private $properties_initialized = false;
	
	/** @var \Closure|null */
	private $properties_evaluator = null;
	
	/** @var \Closure|null */
	private $properties_defaulter = null;
	
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
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\CannotGetWriteonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\InvalidPropertyValue
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		//check
		if ($this->properties_mode === 'w') {
			throw new Exceptions\CannotGetWriteonlyProperty(['object' => $this, 'name' => $name]);
		}
		
		//get
		if (!array_key_exists($name, $this->properties)) {
			//check
			if (!$this->properties_initialized) {
				throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
			}
			
			//load
			$value = isset($this->properties_defaulter) ? ($this->properties_defaulter)($name) : null;
			$valid = ($this->properties_evaluator)($name, $value);
			if (!isset($valid)) {
				throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
			} elseif (!$valid) {
				throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
			}
			$this->properties[$name] = $value;
		}
		return $this->properties[$name];
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
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\InvalidBooleanPropertyValue
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
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\CannotSetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertiesNotInitialized
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertyNotFound
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\InvalidPropertyValue
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		//check
		if ($this->properties_mode === 'r') {
			throw new Exceptions\CannotSetReadonlyProperty(['object' => $this, 'name' => $name]);
		} elseif (!$this->properties_initialized) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		
		//set
		$v = $value;
		$valid = ($this->properties_evaluator)($name, $v);
		if (!isset($valid)) {
			throw new Exceptions\PropertyNotFound(['object' => $this, 'name' => $name]);
		} elseif (!$valid) {
			throw new Exceptions\InvalidPropertyValue(['object' => $this, 'name' => $name, 'value' => $value]);
		}
		$this->properties[$name] = $v;
		
		//return
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\CannotUnsetReadonlyProperty
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\CannotUnsetRequiredProperty
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertiesNotInitialized
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
		}
		unset($this->properties[$name]);
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
		return $this->properties_mode[0] === 'r' ? $this->properties : [];
	}
	
	
	
	//Final private methods
	/**
	 * Initialize a given set of properties with a given evaluator function.
	 * 
	 * @since 1.0.0
	 * @param array $properties <p>The properties to initialize, as <samp>name => value</samp> pairs.</p>
	 * @param callable $evaluator <p>The function to evaluate a given property value for a given name.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (string $name, &$value) : ?bool</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code> : The property name to evaluate for.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b> [reference]</code> : 
	 * The property value to evaluate (validate and sanitize).<br>
	 * <br>
	 * Return: <code><b>bool|null</b></code><br>
	 * Boolean <code>true</code> if the property with the given name and value exists and is valid,
	 * boolean <code>false</code> if it exists but is not valid, or <code>null</code> if it does not exist.
	 * </p>
	 * @param callable|null $defaulter [default = null] <p>The function to retrieve a default property value 
	 * for a given property name.<br>
	 * The expected function signature is represented as:<br><br>
	 * <code>function (string $name)</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code> : The property name to retrieve for.<br>
	 * <br>
	 * Return: <code><b>mixed</b></code><br>
	 * The default value for the given property name.
	 * </p>
	 * @param string[] $required [default = []] <p>The required property names.</p>
	 * @param string $mode [default = 'rw'] <p>The properties read and write mode to initialize with, 
	 * which must be one the following:<br>
	 * &nbsp; &#8226; &nbsp; <samp>rw</samp> : Allow properties to be both read from and written to.<br>
	 * &nbsp; &#8226; &nbsp; <samp>r</samp> : Allow properties to be only read from.<br>
	 * &nbsp; &#8226; &nbsp; <samp>w</samp> : Allow properties to be only written to.
	 * </p>
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\PropertiesAlreadyInitialized
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\InvalidPropertiesMode
	 * @throws \Feralygon\Kit\Core\Traits\LazyProperties\Exceptions\MissingRequiredProperties
	 * @return void
	 */
	final private function initializeProperties(
		array $properties, callable $evaluator, ?callable $defaulter = null, array $required = [], string $mode = 'rw'
	) : void
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
		
		//required
		$this->properties_required = $required;
		if (!empty($required)) {
			$missing = array_keys(array_diff_key(array_flip($required), $properties));
			if (!empty($missing)) {
				throw new Exceptions\MissingRequiredProperties(['object' => $this, 'names' => $missing]);
			}
		}
		
		//evaluator
		UCall::assertSignature('evaluator', $evaluator, function (string $name, &$value) : ?bool {}, true);
		$this->properties_evaluator = \Closure::fromCallable($evaluator);
		
		//defaulter
		if (isset($defaulter)) {
			UCall::assertSignature('defaulter', $defaulter, function (string $name) {}, true);
			$this->properties_defaulter = \Closure::fromCallable($defaulter);
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
