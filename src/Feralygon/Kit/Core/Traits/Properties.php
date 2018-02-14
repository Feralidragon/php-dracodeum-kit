<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

use Feralygon\Kit\Core\Traits\Properties\{
	Manager,
	Objects,
	Exceptions
};
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core properties trait.
 * 
 * This trait enables the support for a separate layer of custom properties in a class.<br>
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.
 * 
 * @since 1.0.0
 */
trait Properties
{
	//Private properties
	/** @var \Feralygon\Kit\Core\Traits\Properties\Manager|null */
	private $properties_manager = null;
	
	
	
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return bool <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name) : bool
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->has($name);
	}
	
	/**
	 * Get property value from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->get($name);
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
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return bool <p>The boolean property value from the given name.</p>
	 */
	final public function is(string $name) : bool
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->is($name);
	}
	
	/**
	 * Check if property is set for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return bool <p>Boolean <code>true</code> if property is set for the given name.</p>
	 */
	final public function isset(string $name) : bool
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->isset($name);
	}
	
	/**
	 * Set property with a given name with a given value.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property value to set with.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		$this->properties_manager->set($name, $value);
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		$this->properties_manager->unset($name);
		return $this;
	}
	
	/**
	 * Get all properties.
	 * 
	 * Only properties which allow read access are returned.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return array <p>All properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll() : array
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->getAll();
	}
	
	
	
	//Final protected methods
	/**
	 * Add a new property with a given name.
	 * 
	 * This method may only be called from within a builder function.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to add.</p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesNotInitialized
	 * @return \Feralygon\Kit\Core\Traits\Properties\Objects\Property 
	 * <p>The newly added property instance with the given name.</p>
	 */
	final protected function addProperty(string $name) : Objects\Property
	{
		if (!isset($this->properties_manager)) {
			throw new Exceptions\PropertiesNotInitialized(['object' => $this]);
		}
		return $this->properties_manager->addProperty($name);
	}
	
	
	
	//Final private methods
	/**
	 * Initialize properties with a given builder function.
	 * 
	 * @since 1.0.0
	 * @param callable $builder <p>The function to build all properties.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function () : void</code><br>
	 * <br>
	 * Return: <code><b>void</b></code>
	 * </p>
	 * @param array $properties [default = []] <p>The properties to initialize with, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param string $mode [default = 'rw'] <p>The base access mode to set for all properties, 
	 * which must be one the following:<br>
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
	 * All properties default to the mode defined here, but if another mode is set in each individual property, 
	 * it becomes restricted as so:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp>, only <samp>r</samp> is allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r+</samp>, only <samp>r</samp> and <samp>r+</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp>, only <samp>w</samp> and <samp>w-</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w-</samp>, only <samp>w-</samp> is allowed.
	 * </p>
	 * @throws \Feralygon\Kit\Core\Traits\Properties\Exceptions\PropertiesAlreadyInitialized
	 * @return void
	 */
	final private function initializeProperties(callable $builder, array $properties = [], string $mode = 'rw') : void
	{
		//manager
		if (isset($this->properties_manager)) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		}
		$this->properties_manager = new Manager($this, false, $mode);
		
		//build
		UCall::assert('builder', $builder, function () : void {}, true);
		$builder();
		
		//initialize
		$this->properties_manager->initialize($properties);
	}
}
