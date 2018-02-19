<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Traits\LazyProperties\{
	Manager,
	Objects,
	Exceptions
};
use Feralygon\Kit\Utilities\Call as UCall;

/**
 * Lazy properties trait.
 * 
 * This trait enables the support for a separate layer of custom lazy-loaded properties in a class.<br>
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.<br>
 * <br>
 * Each and every property is only loaded on demand (lazy-loading).
 * 
 * @since 1.0.0
 */
trait LazyProperties
{
	//Private properties
	/** @var \Feralygon\Kit\Traits\LazyProperties\Manager|null */
	private $properties_manager = null;
	
	/** @var string|null */
	private $properties_builder_current_name = null;
	
	
	
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
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name) : bool
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->has($name);
	}
	
	/**
	 * Get property value from a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @return mixed <p>The property value from the given name.</p>
	 */
	final public function get(string $name)
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->get($name);
	}
	
	/**
	 * Get boolean property value from a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get from.</p>
	 * @return bool <p>The boolean property value from the given name.</p>
	 */
	final public function is(string $name) : bool
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->is($name);
	}
	
	/**
	 * Check if property is set for a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if property is set for the given name.</p>
	 */
	final public function isset(string $name) : bool
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->isset($name);
	}
	
	/**
	 * Set property with a given name with a given value.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to set for.</p>
	 * @param mixed $value <p>The property value to set with.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value)
	{
		$this->guardPropertiesManagerCall();
		$this->properties_manager->set($name, $value);
		return $this;
	}
	
	/**
	 * Unset property from a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to unset from.</p>
	 * @return $this <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name)
	{
		$this->guardPropertiesManagerCall();
		$this->properties_manager->unset($name);
		return $this;
	}
	
	/**
	 * Get all loaded properties.
	 * 
	 * Only properties which allow read access are returned.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @since 1.0.0
	 * @return array <p>All loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll() : array
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->getAll();
	}
	
	/**
	 * Check if properties are read-only.
	 * 
	 * @since 1.0.0
	 * @return bool <p>Boolean <code>true</code> if properties are read-only.</p>
	 */
	final public function isReadonly() : bool
	{
		$this->guardPropertiesManagerCall();
		return $this->properties_manager->isReadonly();
	}
	
	
	
	//Final protected methods
	/**
	 * Create a property instance.
	 * 
	 * This method may only be called after the properties manager initialization and from within a builder function.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Traits\LazyProperties\Exceptions\PropertyCreationFailed
	 * @return \Feralygon\Kit\Traits\LazyProperties\Objects\Property <p>The created property instance.</p>
	 */
	final protected function createProperty() : Objects\Property
	{
		$this->guardPropertiesManagerCall();
		if (!isset($this->properties_builder_current_name)) {
			throw new Exceptions\PropertyCreationFailed(['object' => $this]);
		}
		return $this->properties_manager->createProperty($this->properties_builder_current_name);
	}
	
	
	
	//Final private methods
	/**
	 * Initialize properties with a given builder function.
	 * 
	 * @since 1.0.0
	 * @param callable $builder <p>The function to build a property instance for a given name.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (string $name) : ?\Feralygon\Kit\Traits\LazyProperties\Objects\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code> : The property name to build for.<br>
	 * <br>
	 * Return: <code><b>\Feralygon\Kit\Traits\LazyProperties\Objects\Property|null</b></code><br>
	 * The built property instance for the given name or <code>null</code> if none was built.
	 * </p>
	 * @param array $properties [default = []] <p>The properties to initialize with, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @param string[] $required [default = []] <p>The required property names.</p>
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
	 * @throws \Feralygon\Kit\Traits\LazyProperties\Exceptions\PropertiesAlreadyInitialized
	 * @return void
	 */
	final private function initializeProperties(
		 callable $builder, array $properties = [], array $required = [], string $mode = 'rw'
	) : void
	{
		//manager
		if (isset($this->properties_manager)) {
			throw new Exceptions\PropertiesAlreadyInitialized(['object' => $this]);
		}
		$this->properties_manager = new Manager($this, true, $mode);
		
		//builder
		UCall::assert('builder', $builder, function (string $name) : ?Objects\Property {}, true);
		$this->properties_manager->setBuilder(function (string $name) use ($builder) : ?Objects\Property {
			try {
				$this->properties_builder_current_name = $name;
				return $builder($name);
			} finally {
				$this->properties_builder_current_name = null;
			}
		});
		
		//required
		if (!empty($required)) {
			$this->properties_manager->addRequiredPropertyNames($required);
		}
		
		//initialize
		$this->properties_manager->initialize($properties);
	}
	
	/**
	 * Guard the current function or method in the stack from being called, 
	 * until the properties manager has been initialized.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	final private function guardPropertiesManagerCall() : void
	{
		UCall::guard(
			isset($this->properties_manager),
			"This method may only be called after the properties manager initialization.",
			null, 1
		);
	}
}
