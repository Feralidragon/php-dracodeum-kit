<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a set of methods to check, get, set and unset properties in an object. */
interface Propertiesable
{
	//Public methods
	/**
	 * Check if has property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has property with the given name.</p>
	 */
	public function has(string $name): bool;
	
	/**
	 * Get property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return mixed
	 * <p>The property with the given name.</p>
	 */
	public function get(string $name);
	
	/**
	 * Get boolean property with a given name.
	 * 
	 * This method is an alias of the <code>get</code> method, 
	 * however it is only meant to allow properties which hold boolean values, 
	 * and is simply meant to improve code readability when retrieving boolean properties specifically.
	 * 
	 * @param string $name
	 * <p>The name to get with.</p>
	 * @return bool
	 * <p>The boolean property with the given name.</p>
	 */
	public function is(string $name): bool;
	
	/**
	 * Check if property with a given name is set.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the property with the given name is set.</p>
	 */
	public function isset(string $name): bool;
	
	/**
	 * Set property with a given name and value.
	 * 
	 * @param string $name
	 * <p>The name to set with.</p>
	 * @param mixed $value
	 * <p>The value to set with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function set(string $name, $value): object;
	
	/**
	 * Unset property with a given name.
	 * 
	 * @param string $name
	 * <p>The name to unset with.</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function unset(string $name): object;
	
	/**
	 * Get all properties.
	 * 
	 * @return array
	 * <p>All the properties, as <samp>name => value</samp> pairs.</p>
	 */
	public function getAll(): array;
}
