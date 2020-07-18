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
use Dracodeum\Kit\Interfaces\Properties as IProperties;
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
 * @see \Dracodeum\Kit\Traits\LazyProperties\Keyable
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
	 * Get value from a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return mixed
	 * <p>The value from the property with the given name.</p>
	 */
	final public function __get(string $name)
	{
		return $this->get($name);
	}
	
	/**
	 * Check if a property with a given name is set.
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
	 * Set value in a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @return void
	 */
	final public function __set(string $name, $value): void
	{
		$this->set($name, $value);
	}
	
	/**
	 * Unset value in a property with a given name.
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
	 * Check if has a property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the property with the given name.</p>
	 */
	final public function has(string $name): bool
	{
		return $this->getPropertiesManager()->has($name);
	}
	
	/**
	 * Get value from a property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set value without evaluating it, if currently set as such.</p>
	 * @return mixed
	 * <p>The value from the property with the given name.</p>
	 */
	final public function get(string $name, bool $lazy = false)
	{
		return $this->getPropertiesManager()->get($name, $lazy);
	}
	
	/**
	 * Get boolean value from a property with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, however it only returns a boolean property value, 
	 * and is used to improve code readability when retrieving boolean properties specifically.<br>
	 * <br>
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return bool
	 * <p>The boolean value from the property with the given name.</p>
	 */
	final public function is(string $name): bool
	{
		return $this->getPropertiesManager()->is($name);
	}
	
	/**
	 * Check if a property with a given name is set.
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
	 * Check if a property with a given name is loaded.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is loaded.</p>
	 */
	final public function loaded(string $name): bool
	{
		return $this->getPropertiesManager()->loaded($name);
	}
	
	/**
	 * Check if a property with a given name is initialized.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is initialized.</p>
	 */
	final public function initialized(string $name): bool
	{
		return $this->getPropertiesManager()->initialized($name);
	}
	
	/**
	 * Check if a property with a given name is gettable.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is gettable.</p>
	 */
	final public function gettable(string $name): bool
	{
		return $this->getPropertiesManager()->gettable($name);
	}
	
	/**
	 * Check if a property with a given name is settable.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is settable.</p>
	 */
	final public function settable(string $name): bool
	{
		return $this->getPropertiesManager()->settable($name);
	}
	
	/**
	 * Check if a property with a given name is defaulted.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is defaulted.</p>
	 */
	final public function defaulted(string $name): bool
	{
		return $this->getPropertiesManager()->defaulted($name);
	}
	
	/**
	 * Evaluate a given value with a property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to evaluate with.</p>
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated with the property 
	 * with the given name.</p>
	 */
	final public function eval(string $name, &$value): bool
	{
		return $this->getPropertiesManager()->eval($name, $value);
	}
	
	/**
	 * Set value in a property with a given name.
	 * 
	 * This method may only be called after the properties manager initialization.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set.</p>
	 * @param bool $force [default = false]
	 * <p>Force the given value to be fully evaluated and set, 
	 * even if the property with the given name is set as lazy.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value, bool $force = false): object
	{
		$this->getPropertiesManager()->set($name, $value, $force);
		return $this;
	}
	
	/**
	 * Unset value in a property with a given name.
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
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set values without evaluating them, if currently set as such.</p>
	 * @return array
	 * <p>All the loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll(bool $lazy = false): array
	{
		return $this->getPropertiesManager()->getAll($lazy);
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
	 * @param bool $recursive [default = false]
	 * <p>Check if properties have already been recursively persisted at least once.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if properties have already been persisted at least once.</p>
	 */
	final public function arePropertiesPersisted(bool $recursive = false): bool
	{
		return $this->getPropertiesManager()->isPersisted($recursive);
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
	 * @param \Dracodeum\Kit\Interfaces\Properties $object
	 * <p>The object to set.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function setPropertiesFallbackObject(IProperties $object): object
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
	 * @param bool $lazy [default = false]
	 * <p>Get the lazily set values without evaluating them, if currently set as such.</p>
	 * @return array
	 * <p>All the initializeable loaded properties, as <samp>name => value</samp> pairs.</p>
	 */
	final protected function getAllInitializeable(bool $lazy = false): array
	{
		return $this->getPropertiesManager()->getAllInitializeable($lazy);
	}
	
	/**
	 * Persist properties with a given inserter function and updater function.
	 * 
	 * Only the currently loaded properties are persisted.
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
	 * &nbsp; &nbsp; &nbsp; Automatic properties may not be included in this set, in which case they are required to be 
	 * automatically generated during insertion.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The inserted property values, including all automatically generated ones not set in <var>$values</var>, 
	 * as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to set their corresponding properties with their newly persisted values, 
	 * therefore all automatically generated property values must be returned, whereas any other property value may 
	 * be either returned or not, with any property keeping its current value if a new one is not returned.<br>
	 * <br>
	 * Any returned property values which have no corresponding properties are ignored.</p>
	 * @param callable $updater
	 * <p>The function to use to update from an old given set of property values to a new given set.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $old_values, array $new_values, array $changed_names): array</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $old_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property values to update from, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $new_values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property values to update to, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>string[] $changed_names</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The changed property names to update.<br>
	 * <br>
	 * Return: <code><b>array</b></code><br>
	 * The updated property values, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * All returned property values are used to set their corresponding properties with their newly persisted values, 
	 * therefore any property value may be either returned or not, with any property keeping its current value if a new 
	 * one is not returned.<br>
	 * <br>
	 * Any returned property values which have no corresponding properties are ignored.</p>
	 * @param bool $changes_only [default = false]
	 * <p>Include only changed property values, both old and new, during an update.</p>
	 * @param bool $recursive [default = false]
	 * <p>Persist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function persistProperties(
		callable $inserter, callable $updater, bool $changes_only = false, bool $recursive = false
	): object
	{
		$this->getPropertiesManager()->persist($inserter, $updater, $changes_only, $recursive);
		return $this;
	}
	
	/**
	 * Unpersist properties.
	 * 
	 * @param callable|null $deleter [default = null]
	 * <p>The function to use to delete a given old set of property values.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function (array $values): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $values</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The property values to delete, as <samp>name => value</samp> pairs.<br>
	 * <br>
	 * Return: <code><b>void</b></code></p>
	 * @param bool $recursive [default = false]
	 * <p>Unpersist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function unpersistProperties(?callable $deleter = null, bool $recursive = false): object
	{
		$this->getPropertiesManager()->unpersist($deleter, $recursive);
		return $this;
	}
	
	/**
	 * Add pre-persistent property change callback function for a given property name.
	 * 
	 * All pre-persistent property change callback functions are called immediately before the corresponding property 
	 * value change is persisted or unpersisted.
	 * 
	 * @param string $name
	 * <p>The property name to add for.</p>
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($old_value, $new_value): void</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $old_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of a deletion.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addPrePersistentPropertyChangeCallback(string $name, callable $callback): object
	{
		$this->getPropertiesManager()->addPrePersistentChangeCallback($name, $callback);
		return $this;
	}
	
	/**
	 * Add post-persistent property change callback function for a given property name.
	 * 
	 * All post-persistent property change callback functions are called immediately after the corresponding property 
	 * value change is persisted or unpersisted.
	 * 
	 * @param string $name
	 * <p>The property name to add for.</p>
	 * @param callable $callback
	 * <p>The callback function to add.<br>
	 * It is expected to be compatible with the following signature:<br>
	 * <br>
	 * <code>function ($old_value, $new_value): void</code><br>
	 * <br>
	 *Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $old_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The old property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of an insertion.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>mixed $new_value</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The new property value.<br>
	 * &nbsp; &nbsp; &nbsp; The value <code>null</code> is given in the case of a deletion.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final protected function addPostPersistentPropertyChangeCallback(string $name, callable $callback): object
	{
		$this->getPropertiesManager()->addPostPersistentChangeCallback($name, $callback);
		return $this;
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
	 * <p>The properties remainder.<br>
	 * If set, then it is filled with all remaining properties which have not been found from the given 
	 * <var>$properties</var> above, as <samp>name => value</samp> pairs or 
	 * an array of required property values or both.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final private function initializeProperties(
		callable $builder, array $properties = [], ?callable $required_names_loader = null, string $mode = 'rw', 
		bool $persisted = false, ?callable $remainderer = null, ?array &$remainder = null
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
