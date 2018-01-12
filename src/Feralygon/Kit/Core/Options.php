<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Options\Exceptions;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core options class.
 * 
 * This class is the base to be extended from when creating options.<br>
 * <br>
 * An options instance is a simple object which holds a set of properties of different types, 
 * and is meant to be mainly used within a class method or function, by representing an additional set of optional parameters.<br>
 * All properties are validated and sanitized, guaranteeing their type and integrity, 
 * and may be retrieved and modified directly just like public object properties.
 * 
 * @since 1.0.0
 */
abstract class Options implements \ArrayAccess
{
	//Traits
	use Traits\PropertiesArrayAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 */
	final public function __construct(array $properties = [])
	{
		$this->initializeProperties($properties, \Closure::fromCallable([$this, 'evaluateProperty']));
	}
	
	
	
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
	
	
	
	//Final public static methods
	/**
	 * Load an instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Core\Options|array|null $options [default = null] <p>An instance or properties, as <samp>name => value</samp> pairs, to load with.</p>
	 * @param bool $clone [default = false] <p>Clone the given instance into a new one with the same properties.</p>
	 * @throws \Feralygon\Kit\Core\Options\Exceptions\LoadFailed
	 * @return static <p>An instance of this class.</p>
	 */
	final public static function load($options = null, bool $clone = false) : Options
	{
		if (!isset($options) || is_array($options)) {
			return new static($options ?? []);
		} elseif (is_object($options)) {
			if (!$clone && get_class($options) === static::class) {
				return $options;
			} elseif (UType::isA($options, self::class)) {
				return new static($options->getLoadedProperties());
			}
		}
		throw new Exceptions\LoadFailed(['class' => static::class, 'options' => $options]);
	}
}
