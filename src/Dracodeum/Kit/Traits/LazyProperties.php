<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Traits\LazyProperties\{
	Manager,
	Property
};
use Dracodeum\Kit\Interfaces\Propertiesable as IPropertiesable;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait enables the support for a separate layer of custom lazy-loaded properties in a class.
 * 
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.<br>
 * Each and every property is only loaded on demand (lazy-loading).
 * 
 * @see \Dracodeum\Kit\Traits\LazyProperties\ArrayAccess
 */
trait LazyProperties
{
	//Private properties
	/** @var \Dracodeum\Kit\Traits\LazyProperties\Manager|null */
	private $properties_manager = null;
	
	/** @var string|null */
	private $properties_builder_current_name = null;
	
	
	
	//Final public magic methods
	/**
	 * Get property value with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return mixed
	 * <p>The property value with the given name.</p>
	 */
	final public function __get(string $name)
	{
		return $this->get($name);
	}
	
	/**
	 * Check if property with a given name is set.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is set.</p>
	 */
	final public function __isset(string $name): bool
	{
		return $this->isset($name);
	}
	
	/**
	 * Set property with a given name and value.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return void
	 */
	final public function __set(string $name, $value): void
	{
		$this->set($name, $value);
	}
	
	/**
	 * Unset property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return void
	 */
	final public function __unset(string $name): void
	{
		$this->unset($name);
	}
	
	
	
	//Final public methods
	/**
	 * Check if has property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name): bool
	{
		return $this->getPropertiesManager()->has($name);
	}
	
	/**
	 * Get property value with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return mixed
	 * <p>The property value with the given name.</p>
	 */
	final public function get(string $name)
	{
		return $this->getPropertiesManager()->get($name);
	}
	
	/**
	 * Get boolean property value with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it only allows properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return bool
	 * <p>The boolean property value with the given name.</p>
	 */
	final public function is(string $name): bool
	{
		return $this->getPropertiesManager()->is($name);
	}
	
	/**
	 * Check if property with a given name is set.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is set.</p>
	 */
	final public function isset(string $name): bool
	{
		return $this->getPropertiesManager()->isset($name);
	}
	
	/**
	 * Set property with a given name and value.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value): object
	{
		$this->getPropertiesManager()->set($name, $value);
		return $this;
	}
	
	/**
	 * Unset property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name): object
	{
		$this->getPropertiesManager()->unset($name);
		return $this;
	}
	
	/**
	 * Get all loaded properties.
	 * 
	 * Only properties which allow read access are returned.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return array
	 * <p>All the loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll(): array
	{
		return $this->getPropertiesManager()->getAll();
	}
	
	/**
	 * Check if properties are read-only.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if properties are read-only.</p>
	 */
	final public function arePropertiesReadonly(): bool
	{
		return $this->getPropertiesManager()->isReadonly();
	}
	
	
	
	//Final protected methods
	/**
	 * Create a property instance.
	 * 
	 * This method may only be called after the properties manager initialization and from a builder function.<br>
	 * <br>
	 * The property name is given and set automatically.
	 * 
	 * @return \Dracodeum\Kit\Traits\LazyProperties\Property
	 * <p>The created property instance.</p>
	 */
	final protected function createProperty(): Property
	{
		UCall::guard(isset($this->properties_builder_current_name), [
			'hint_message' => "This method may only be called after the properties manager initialization and " . 
				"from within a builder function."
		]);
		return $this->getPropertiesManager()->createProperty($this->properties_builder_current_name);
	}
	
	/**
	 * Add required property name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addRequiredPropertyName(string $name): object
	{
		$this->getPropertiesManager()->addRequiredPropertyName($name);
		return $this;
	}
	
	/**
	 * Add required property names.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string[] $names
	 * <p>The names to add.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addRequiredPropertyNames(array $names): object
	{
		$this->getPropertiesManager()->addRequiredPropertyNames($names);
		return $this;
	}
	
	/**
	 * Set properties as read-only.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function setPropertiesAsReadonly(): object
	{
		$this->getPropertiesManager()->setAsReadonly();
		return $this;
	}
	
	/**
	 * Set properties fallback object.
	 * 
	 * By setting a properties fallback object, any property not found in this object is attempted to be got from 
	 * the given fallback object instead.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param \Dracodeum\Kit\Interfaces\Propertiesable $object
	 * <p>The object to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function setPropertiesFallbackObject(IPropertiesable $object): object
	{
		$this->getPropertiesManager()->setFallbackObject($object);
		return $this;
	}
	
	/**
	 * Unset properties fallback object.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function unsetPropertiesFallbackObject(): object
	{
		$this->getPropertiesManager()->unsetFallbackObject();
		return $this;
	}
	
	/**
	 * Get properties debug info.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @see https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
	 * @return array
	 * <p>The properties debug info.</p>
	 */
	final protected function getPropertiesDebugInfo(): array
	{
		return $this->getPropertiesManager()->getDebugInfo();
	}
	
	/**
	 * Process a given properties debug info instance.
	 * 
	 * @param \Dracodeum\Kit\Traits\DebugInfo\Info $info
	 * <p>The debug info instance to process.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function processPropertiesDebugInfo(DebugInfo $info): object
	{
		//debug info
		$debug_info = $this->getPropertiesDebugInfo();
		if (!empty($debug_info)) {
			$info->set('@properties', $debug_info);
		}
		
		//hidden properties
		$info
			->hideObjectProperty('properties_manager', self::class)
			->hideObjectProperty('properties_builder_current_name', self::class)
		;
		
		//return
		return $this;
	}
	
	/**
	 * Get all initializeable loaded properties.
	 * 
	 * Only properties which are allowed to be initialized with are returned.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return array
	 * <p>All the initializeable loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final protected function getAllInitializeable(): array
	{
		return $this->getPropertiesManager()->getAllInitializeable();
	}
	
	
	
	//Final private methods
	/**
	 * Initialize properties with a given builder function.
	 * 
	 * @param callable $builder
	 * <p>The function to use to build a property instance with a given name.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (string $name): ?Dracodeum\Kit\Traits\LazyProperties\Property</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string $name</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The name to build with.<br>
	 * <br>
	 * Return: <code><b>Dracodeum\Kit\Traits\LazyProperties\Property|null</b></code><br>
	 * The built property instance with the given name or <code>null</code> if none was built.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to initialize with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @param callable|null $required_names_loader [default = null]
	 * <p>The function to use to load required property names.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): void</code><br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
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
	 * All properties default to the mode defined here, but if another mode is set in each individual property, 
	 * it becomes restricted as so:<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>r</samp> or <samp>r+</samp>, 
	 * only <samp>r</samp>, <samp>r+</samp> and <samp>rw</samp> are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>rw</samp>, all modes are allowed;<br>
	 * &nbsp; &#8226; &nbsp; if set to <samp>w</samp>, <samp>w-</samp> or <samp>w--</samp>, 
	 * only <samp>rw</samp>, <samp>w</samp>, <samp>w-</samp> and <samp>w--</samp> are allowed.</p>
	 * @param callable|null $remainderer [default = null]
	 * <p>The function to use to handle a given set of remaining properties.<br>
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
	 * @param array|null $remainder [reference output] [default = null]
	 * <p>The properties remainder, which, if set, is gracefully filled with all remaining properties which have 
	 * not been found from the given <var>$properties</var> above, as <samp>name => value</samp> pairs or 
	 * an array of required property values or both.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function initializeProperties(
		callable $builder, array $properties = [], ?callable $required_names_loader = null, string $mode = 'rw', 
		?callable $remainderer = null, ?array &$remainder = null
	): object
	{
		//initialize
		UCall::guard(!isset($this->properties_manager) || !$this->properties_manager->isInitialized(), [
			'error_message' => "Properties have already been initialized."
		]);
		$this->properties_manager = new Manager($this, true, $mode);
		
		//builder
		UCall::assert('builder', $builder, function (string $name): ?Property {});
		$this->properties_manager->setBuilder(function (string $name) use ($builder): ?Property {
			try {
				$this->properties_builder_current_name = $name;
				return $builder($name);
			} finally {
				$this->properties_builder_current_name = null;
			}
		});
		
		//required names loader
		if (isset($required_names_loader)) {
			UCall::assert('required_names_loader', $required_names_loader, function (): void {});
			$required_names_loader();
		}
		
		//remainderer
		if (isset($remainderer)) {
			$this->properties_manager->setRemainderer($remainderer);
		}
		
		//initialize
		$this->properties_manager->initialize($properties, $remainder);
		
		//return
		return $this;
	}
	
	/**
	 * Get properties manager instance.
	 * 
	 * This method also guards the current function or method in the stack from being called until the properties 
	 * manager has been initialized.
	 * 
	 * @return \Dracodeum\Kit\Traits\LazyProperties\Manager
	 * <p>The properties manager instance.</p>
	 */
	final private function getPropertiesManager(): Manager
	{
		UCall::guard(isset($this->properties_manager), [
			'hint_message' => "This method may only be called after the properties manager initialization.",
			'stack_offset' => 1
		]);
		return $this->properties_manager;
	}
}