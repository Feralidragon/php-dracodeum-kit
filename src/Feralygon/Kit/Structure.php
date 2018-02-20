<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\Arrayable as IArrayable;
use Feralygon\Kit\Structure\Exceptions;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * Structure class.
 * 
 * This class is the base to be extended from when creating a structure.<br>
 * <br>
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be retrieved and modified directly just like any public object property, 
 * and may also be set to read-only during instantiation to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 */
abstract class Structure implements \ArrayAccess, IArrayable
{
	//Traits
	use Traits\Properties\ArrayableAccess;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []] <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @param bool $readonly [default = false] <p>Set all properties as read-only.</p>
	 */
	final public function __construct(array $properties = [], bool $readonly = false)
	{
		$mode = $readonly ? 'r+' : 'rw';
		$this->initializeProperties(\Closure::fromCallable([$this, 'buildProperties']), $properties, $mode);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Build properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function buildProperties() : void;
	
	
	
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
	 * @param bool $readonly [default = false] <p>Set all properties of a new instance as read-only.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $clone = false, bool $readonly = false, bool $nullable = false
	) : bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly, $nullable);
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
	 * @param bool $readonly [default = false] <p>Set all properties of a new instance as read-only.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Structure\Exceptions\CoercionFailed
	 * @return static|null <p>The given value coerced into an instance.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, bool $clone = false, bool $readonly = false, bool $nullable = false
	) : ?Structure
	{
		try {
			if (!isset($value)) {
				return $nullable ? null : new static([], $readonly);
			} elseif (is_array($value)) {
				return new static($value, $readonly);
			} elseif (is_object($value)) {
				if (!$clone && get_class($value) === static::class) {
					return $value;
				} elseif (UType::isA($value, self::class)) {
					return new static($value->getAll(), $readonly);
				}
			}
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'structure' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'structure' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only null, an instance or array of properties, " . 
				"given as \"name => value\" pairs, can be coerced into an instance."
		]);
	}
}
