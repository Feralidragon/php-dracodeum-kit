<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Traits\Properties\{
	Manager,
	Property
};
use Dracodeum\Kit\Interfaces\Propertiesable as IPropertiesable;
use Dracodeum\Kit\Traits\DebugInfo\Info as DebugInfo;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait enables the support for a separate layer of custom properties in a class.
 * 
 * All these properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be accessed and modified directly just like public object properties.
 * 
 * @see \Dracodeum\Kit\Traits\Properties\Arrayable
 * @see \Dracodeum\Kit\Traits\Properties\ArrayAccess
 */
trait Properties
{
	//Private properties
	/** @var \Dracodeum\Kit\Traits\Properties\Manager|null */
	private $properties_manager = null;
	
	
	
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
	 * Get all properties.
	 * 
	 * Only properties which allow read access are returned.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return array
	 * <p>All the properties, as <samp>name => value</samp> pairs.</p>
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
	
	/**
	 * Check if properties have already been persisted at least once.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if properties have already been persisted at least once.</p>
	 */
	final public function arePropertiesPersisted(): bool
	{
		return $this->getPropertiesManager()->isPersisted();
	}
	
	
	
	//Final protected methods
	/**
	 * Add a new property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization and from within a builder function.
	 * 
	 * @param string $name
	 * <p>The name to add with.</p>
	 * @return \Dracodeum\Kit\Traits\Properties\Property
	 * <p>The newly added property instance with the given name.</p>
	 */
	final protected function addProperty(string $name): Property
	{
		return $this->getPropertiesManager()->addProperty($name);
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
		$debug_info = $this->getPropertiesDebugInfo();
		if (!empty($debug_info)) {
			$info->set('@properties', $debug_info);
		}
		$info->hideObjectProperty('properties_manager', self::class);
		return $this;
	}
	
	/**
	 * Get all initializeable properties.
	 * 
	 * Only properties which are allowed to be initialized with are returned.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @return array
	 * <p>All the initializeable properties, as <samp>name => value</samp> pairs.</p>
	 */
	final protected function getAllInitializeable(): array
	{
		return $this->getPropertiesManager()->getAllInitializeable();
	}
	
	/**
	 * Persist properties with a given inserter function and updater function.
	 * 
	 * @param callable $inserter
	 * <p>The function to use to insert a new given set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $values): array</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property values to insert, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &nbsp; &nbsp; Automatic properties are not included in this set, being required to be automatically 
	 * generated during insertion.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The inserted property values, including all automatically generated ones not set in <var>$values</var>, 
	 * as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to reset their corresponding properties to their newly persisted values, 
	 * thus all automatically generated property values must be returned, whereas any other property value may 
	 * optionally be either returned or not, with any corresponding property keeping its current value if a new one is 
	 * not returned.<br>
	 * <br>
	 * Any returned property values that have no corresponding properties are ignored.</p>
	 * @param callable $updater
	 * <p>The function to use to update from a given old set of property values to a new given set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $old_values, array $new_values): array</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $old_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property values to update from, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $new_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property values to update to, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The updated property values, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to reset their corresponding properties to their newly persisted values, 
	 * thus any property value may optionally be either returned or not, with any corresponding property keeping its 
	 * current value if a new one is not returned.<br>
	 * <br>
	 * Any returned property values that have no corresponding properties are ignored.</p>
	 * @param bool $update_changes_only [default = false]
	 * <p>Include only changed property values, both old and new, during an update.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function persistProperties(
		callable $inserter, callable $updater, bool $update_changes_only = false
	): object
	{
		$this->getPropertiesManager()->persist($inserter, $updater, $update_changes_only);
		return $this;
	}
	
	
	
	//Final private methods
	/**
	 * Initialize properties with a given builder function.
	 * 
	 * @param callable $builder
	 * <p>The function to use to build all properties.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (): void</code></p>
	 * @param array $properties [default = []]
	 * <p>The properties to initialize with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
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
	 * @param bool $persisted [default = false]
	 * <p>Set properties as having already been persisted at least once.</p>
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
		callable $builder, array $properties = [], string $mode = 'rw', bool $persisted = false, 
		?callable $remainderer = null, ?array &$remainder = null
	): object
	{
		//initialize
		UCall::guard(!isset($this->properties_manager) || !$this->properties_manager->isInitialized(), [
			'error_message' => "Properties have already been initialized."
		]);
		$this->properties_manager = new Manager($this, false, $mode);
		
		//build
		UCall::assert('builder', $builder, function (): void {});
		$builder();
		
		//remainderer
		if (isset($remainderer)) {
			$this->properties_manager->setRemainderer($remainderer);
		}
		
		//initialize
		$this->properties_manager->initialize($properties, $persisted, $remainder);
		
		//return
		return $this;
	}
	
	/**
	 * Get properties manager instance.
	 * 
	 * This method also guards the current function or method in the stack from being called until the properties 
	 * manager has been initialized.
	 * 
	 * @return \Dracodeum\Kit\Traits\Properties\Manager
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
