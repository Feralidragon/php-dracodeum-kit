<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\ArrayInstantiable as IArrayInstantiable;
use Feralygon\Kit\Options\{
	Traits,
	Exceptions
};
use Feralygon\Kit\Traits as KitTraits;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * This class is the base to be extended from when creating options.
 * 
 * An options instance is a simple object which holds a set of properties of different types, 
 * and is meant to be mainly used within a class method or function, 
 * by representing an additional set of optional parameters.<br>
 * <br>
 * All properties are lazy-loaded, and validated and sanitized, guaranteeing their type and integrity, 
 * and may be got and set directly just like public object properties.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Options\Traits\DefaultBuilder
 */
abstract class Options implements \ArrayAccess, IArrayInstantiable
{
	//Traits
	use KitTraits\LazyProperties\ArrayAccess;
	use KitTraits\Readonly;
	use Traits\DefaultBuilder;
	
	
	
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
		$this->initializeProperties(\Closure::fromCallable([$this, 'buildProperty']), $properties, null, $mode);
		
		//read-only
		$this->initializeReadonly(
			$readonly, $readonly ? [] : [\Closure::fromCallable([$this, 'setPropertiesAsReadonly'])]
		);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Build property instance with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to build with.</p>
	 * @return \Feralygon\Kit\Traits\LazyProperties\Property|null
	 * <p>The built property instance with the given name or <code>null</code> if none was built.</p>
	 */
	abstract protected function buildProperty(string $name): ?Property;
	
	
	
	//Implemented final public static methods (Feralygon\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return new static($array);
	}
	
	
	
	//Final public methods
	/**
	 * Clone into a new instance.
	 * 
	 * The returning cloned instance is a new instance with the same properties.
	 * 
	 * @since 1.0.0
	 * @param bool|null $readonly [default = null]
	 * <p>Set the new cloned instance as read-only.<br>
	 * If not set, then the new cloned instance read-only state is set to match the one from this instance.</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	final public function clone(?bool $readonly = null): Options
	{
		return new static($this->getAll(), $readonly ?? $this->isReadonly());
	}
	
	
	
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
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Evaluate into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same properties and read-only state.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Options</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $clone = false, ?bool $readonly = null, ?callable $builder = null, bool $nullable = false
	): bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly, $builder, $nullable);
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
	 * <p>If an instance is given, then clone it into a new one with the same properties.</p>
	 * @param bool|null $readonly [default = null]
	 * <p>Coerce into either a non-read-only or read-only instance.<br>
	 * If set and if an instance is given and its read-only state does not match, 
	 * then a new one is created with the same properties and read-only state.</p>
	 * @param callable|null $builder [default = null]
	 * <p>The function to use to build an instance.<br>
	 * It is expected to be compatible with the following signature:<br><br>
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Options</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Options</b></code><br>
	 * The built instance.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Options\Exceptions\CoercionFailed
	 * @return static|null
	 * <p>The given value coerced into an instance.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce(
		$value, bool $clone = false, ?bool $readonly = null, ?callable $builder = null, bool $nullable = false
	): ?Options
	{
		//builder
		if (!isset($builder)) {
			$builder = static::getDefaultBuilder();
		}
		if (isset($builder)) {
			UCall::assert('builder', $builder, function (array $properties, bool $readonly): Options {});
		}
		
		//coerce
		try {
			if ($nullable && !isset($value)) {
				return null;
			} elseif (!isset($value) || is_array($value)) {
				return isset($builder)
					? UType::coerceObject($builder($value ?? [], $readonly ?? false), static::class)
					: new static($value ?? [], $readonly ?? false);
			} elseif (is_object($value) && $value instanceof Options) {
				if ($clone || (isset($readonly) && $readonly !== $value->isReadonly())) {
					return $value->clone($readonly);
				} elseif (!UType::isA($value, static::class)) {
					return isset($builder)
						? UType::coerceObject($builder($value->getAll(), $readonly ?? false), static::class)
						: new static($value->getAll(), $readonly ?? false);
				}
				return $value;
			}
		} catch (\Exception $exception) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'options' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//throw
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'options' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only null, an instance or array of properties, " . 
				"given as \"name => value\" pairs, can be coerced into an instance."
		]);
	}
}
