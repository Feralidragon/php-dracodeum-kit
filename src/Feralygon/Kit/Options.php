<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Options\Exceptions;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * This class is the base to be extended from when creating options.
 * 
 * An options instance is a simple object which holds a set of properties of different types, 
 * and is meant to be mainly used within a class method or function, 
 * by representing an additional set of optional parameters.<br>
 * <br>
 * All properties are lazy-loaded, and validated and sanitized, guaranteeing their type and integrity, 
 * and may be retrieved and modified directly just like public object properties.<br>
 * <br>
 * It may also be set to read-only during instantiation to prevent any further changes.
 * 
 * @since 1.0.0
 */
abstract class Options implements \ArrayAccess
{
	//Traits
	use Traits\LazyProperties\ArrayAccess;
	use Traits\Readonly;
	
	
	
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @since 1.0.0
	 * @param array $properties [default = []]
	 * <p>The properties, as <samp>name => value</samp> pairs.</p>
	 * @param bool $readonly [default = false]
	 * <p>Set as read-only.</p>
	 */
	final public function __construct(array $properties = [], bool $readonly = false)
	{
		//properties
		$mode = $readonly ? 'r+' : 'rw';
		$this->initializeProperties(\Closure::fromCallable([$this, 'buildProperty']), $properties, [], $mode);
		
		//read-only
		$this->initializeReadonly(
			$readonly,
			$readonly ? [] : [\Closure::fromCallable([$this, 'setPropertiesAsReadonly'])]
		);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Build property instance for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to build for.</p>
	 * @return \Feralygon\Kit\Traits\LazyProperties\Objects\Property|null
	 * <p>The built property instance for the given name or <code>null</code> if none was built.</p>
	 */
	abstract protected function buildProperty(string $name) : ?Property;
	
	
	
	//Final public static methods
	/**
	 * Evaluate a given value as an instance.
	 * 
	 * Only <code>null</code>, an instance or array of properties, given as <samp>name => value</samp> pairs, 
	 * can be evaluated into an instance.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Evaluate into a read-only instance.<br>
	 * If an instance is given and is not read-only, a new one is created with the same properties and as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(&$value, bool $clone = false, bool $readonly = false) : bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly);
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
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $clone [default = false]
	 * <p>If an instance is given, clone it into a new one with the same properties.</p>
	 * @param bool $readonly [default = false]
	 * <p>Coerce into a read-only instance.<br>
	 * If an instance is given and is not read-only, a new one is created with the same properties and as read-only.</p>
	 * @throws \Feralygon\Kit\Options\Exceptions\CoercionFailed
	 * @return static
	 * <p>The given value coerced into an instance.</p>
	 */
	final public static function coerce($value, bool $clone = false, bool $readonly = false) : Options
	{
		try {
			if (!isset($value)) {
				return new static([], $readonly);
			} elseif (is_array($value)) {
				return new static($value, $readonly);
			} elseif (is_object($value) && $value instanceof Options) {
				return $clone || ($readonly && !$value->isReadonly()) || !UType::isA($value, static::class)
					? new static($value->getAll(), $readonly)
					: $value;
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
