<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

/**
 * Core immutable class.
 * 
 * This class is the base to be extended from when creating an immutable.<br>
 * <br>
 * An immutable object represents and stores multiple properties of multiple types 
 * in such a way that none of them can ever be modified after instantiation.<br>
 * Each and every single one of its properties is lazy-loaded and is validated and sanitized, 
 * guaranteeing its type and integrity, and may be retrieved directly just like any public object property.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Immutable_object
 */
abstract class Immutable implements \ArrayAccess
{
	//Traits
	use Traits\LazyPropertiesArrayAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [])
	{
		$this->initializeProperties(
			$properties,
			\Closure::fromCallable([$this, 'evaluateProperty']),
			\Closure::fromCallable([$this, 'getDefaultPropertyValue']),
			$this->getRequiredPropertyNames(), 'r'
		);
	}
	
	
	
	//Abstract public static methods
	/**
	 * Get required property names.
	 * 
	 * All the required properties returned here must be given during instantiation.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The required property names.</p>
	 */
	abstract public static function getRequiredPropertyNames() : array;
	
	
	
	//Abstract protected methods	
	/**
	 * Evaluate a given property value for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to evaluate for.</p>
	 * @param mixed $value [reference] <p>The property value to evaluate (validate and sanitize).</p>
	 * @return bool|null <p>Boolean <code>true</code> if the property with the given name and value exists and is valid,
	 * boolean <code>false</code> if it exists but is not valid, or <code>null</code> if it does not exist.</p>
	 */
	abstract protected function evaluateProperty(string $name, &$value) : ?bool;
	
	
	
	//Protected methods
	/**
	 * Get default value for a given property name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The property name to get for.</p>
	 * @return mixed <p>The default value for the given property name.</p>
	 */
	protected function getDefaultPropertyValue(string $name)
	{
		return null;
	}
}
