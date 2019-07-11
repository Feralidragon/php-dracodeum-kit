<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo;

use Feralygon\Kit\Traits;
use Feralygon\Kit\Traits\DebugInfo\Info\Exceptions;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * This class represents the object used to configure the properties to set up in the debug info of a given object.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\DebugInfo
 */
final class Info
{
	//Traits
	use Traits\NoConstructor;
	
	
	
	//Private constants
	/** Class wildcard. */
	private const CLASS_WILDCARD = '*';
	
	
	
	//Private properties
	/** @var array */
	private $properties = [];
	
	/** @var bool */
	private $object_properties_dump = false;
	
	/** @var bool[] */
	private $ignored_object_properties_map = [];
	
	
	
	//Final public methods
	/**
	 * Check if has property with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	final public function has(string $name): bool
	{
		return array_key_exists($name, $this->properties);
	}
	
	/**
	 * Get property value with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Traits\DebugInfo\Info\Exceptions\PropertyNotFound
	 * @return mixed
	 * <p>The property value with the given name.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if it was not found.</p>
	 */
	final public function get(string $name, bool $no_throw = false)
	{
		if (!$this->hasProperty($name)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\PropertyNotFound([$this, $name]);
		}
		return $this->properties[$name];
	}
	
	/**
	 * Get all properties.
	 * 
	 * @since 1.0.0
	 * @return array
	 * <p>All the properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function getAll(): array
	{
		return $this->properties;
	}
	
	/**
	 * Set property with a given name and value.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function set(string $name, $value): Info
	{
		$this->properties[$name] = $value;
		return $this;
	}
	
	/**
	 * Set all properties.
	 * 
	 * @since 1.0.0
	 * @param array $properties
	 * <p>All the properties to set, as <samp>name => value</samp> pairs.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setAll(array $properties): Info
	{
		$this->properties = $properties;
		return $this;
	}
	
	/**
	 * Unset property with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unset(string $name): Info
	{
		unset($this->properties[$name]);
		return $this;
	}
	
	/**
	 * Clear all properties.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function clear(): Info
	{
		$this->properties = [];
		return $this;
	}
	
	/**
	 * Check if the dump of object properties is enabled.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if the dump of object properties is enabled.</p>
	 */
	final public function isObjectPropertiesDumpEnabled(): bool
	{
		return $this->object_properties_dump;
	}
	
	/**
	 * Enable the dump of object properties.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function enableObjectPropertiesDump(): Info
	{
		$this->object_properties_dump = true;
		return $this;
	}
	
	/**
	 * Disable the dump of object properties.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function disableObjectPropertiesDump(): Info
	{
		$this->object_properties_dump = false;
		return $this;
	}
	
	/**
	 * Check if an object property with a given name is ignored.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @param string|null $class [default = null]
	 * <p>The class to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the object property with the given name is ignored.</p>
	 */
	final public function isObjectPropertyIgnored(string $name, ?string $class = null): bool
	{
		$class = isset($class) ? UType::class($class) : self::CLASS_WILDCARD;
		return isset($this->ignored_object_properties_map[$class][$name]);
	}
	
	/**
	 * Set object property with a given name as ignored.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param string|null $class [default = null]
	 * <p>The class to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function setObjectPropertyAsIgnored(string $name, ?string $class = null): Info
	{
		$class = isset($class) ? UType::class($class) : self::CLASS_WILDCARD;
		$this->ignored_object_properties_map[$class][$name] = true;
		return $this;
	}
	
	/**
	 * Unset object property with a given name as ignored.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @param string|null $class [default = null]
	 * <p>The class to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	final public function unsetObjectPropertyAsIgnored(string $name, ?string $class = null): Info
	{
		$class = isset($class) ? UType::class($class) : self::CLASS_WILDCARD;
		unset($this->ignored_object_properties_map[$class][$name]);
		if (empty($this->ignored_object_properties_map[$class])) {
			unset($this->ignored_object_properties_map[$class]);
		}
		return $this;
	}
}
