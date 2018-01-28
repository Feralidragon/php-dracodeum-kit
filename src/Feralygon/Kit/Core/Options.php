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
 * and is meant to be mainly used within a class method or function, 
 * by representing an additional set of optional parameters.<br>
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
	 * Evaluate a given value as an instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $clone [default = false] <p>If an instance is given, 
	 * clone it into a new one with the same properties.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(&$value, bool $clone = false, bool $nullable = false) : bool
	{
		try {
			$value = static::coerce($value, $clone, $nullable);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be coerced into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $clone [default = false] <p>If an instance is given, 
	 * clone it into a new one with the same properties.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Options\Exceptions\CoercionFailed
	 * @return static|null <p>The given value coerced into an instance.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerce($value, bool $clone = false, bool $nullable = false) : ?Options
	{
		try {
			if (!isset($value)) {
				return $nullable ? null : new static();
			} elseif (is_array($value)) {
				return new static($value);
			} elseif (is_object($value)) {
				if (!$clone && get_class($value) === static::class) {
					return $value;
				} elseif (UType::isA($value, self::class)) {
					return new static($value->getLoadedProperties());
				}
			}
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'options' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'options' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only null, an instance or array of properties, " . 
				"given as \"name => value\" pairs, can be coerced into an instance."
		]);
	}
}
