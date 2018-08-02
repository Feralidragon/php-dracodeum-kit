<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\{
	Arrayable as IArrayable,
	ArrayInstantiable as IArrayInstantiable,
	Stringifiable as IStringifiable
};
use Feralygon\Kit\Structure\{
	Traits,
	Exceptions
};
use Feralygon\Kit\Traits as KitTraits;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Utilities\{
	Call as UCall,
	Text as UText,
	Type as UType
};

/**
 * This class is the base to be extended from when creating a structure.
 * 
 * A structure is a simple object which represents and stores multiple properties of multiple types.<br>
 * Each and every single one of its properties is validated and sanitized, guaranteeing its type and integrity, 
 * and may be got and set directly just like any public object property.<br>
 * <br>
 * It may also be set as read-only to prevent any further changes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Struct_(C_programming_language)
 * @see \Feralygon\Kit\Structure\Traits\DefaultBuilder
 */
abstract class Structure implements \ArrayAccess, \JsonSerializable, IArrayable, IArrayInstantiable, IStringifiable
{
	//Traits
	use KitTraits\Properties\ArrayableAccess;
	use KitTraits\Readonly;
	use KitTraits\Stringifiable;
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
		$this->initializeProperties(\Closure::fromCallable([$this, 'loadProperties']), $properties, $mode);
		
		//read-only
		$this->initializeReadonly(
			$readonly, $readonly ? [] : [\Closure::fromCallable([$this, 'setPropertiesAsReadonly'])]
		);
	}
	
	
	
	//Abstract protected methods
	/**
	 * Load properties.
	 * 
	 * @since 1.0.0
	 * @return void
	 */
	abstract protected function loadProperties(): void;
	
	
	
	//Implemented final public methods (JsonSerializable)
	/** {@inheritdoc} */
	final public function jsonSerialize()
	{
		return $this->getAll();
	}
	
	
	
	//Implemented final public static methods (Feralygon\Kit\Interfaces\ArrayInstantiable)
	/** {@inheritdoc} */
	final public static function fromArray(array $array): object
	{
		return new static($array);
	}
	
	
	
	//Implemented public methods (Feralygon\Kit\Interfaces\Stringifiable)
	/** {@inheritdoc} */
	public function toString(?TextOptions $text_options = null): string
	{
		return UText::stringify($this->getAll(), $text_options);
	}
	
	
	
	//Final public methods
	/**
	 * Clone into a new instance.
	 * 
	 * The returning cloned instance is a new instance with the same properties.
	 * 
	 * @since 1.0.0
	 * @param bool $readonly [default = false]
	 * <p>Set the new cloned instance as read-only.</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	final public function clone(bool $readonly = false): Structure
	{
		return new static($this->getAll(), $readonly);
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
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Structure</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Structure</b></code><br>
	 * The built instance.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an instance.</p>
	 */
	final public static function evaluate(
		&$value, bool $clone = false, ?bool $readonly = null, ?callable $builder = null
	): bool
	{
		try {
			$value = static::coerce($value, $clone, $readonly, $builder);
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
	 * <code>function (array $properties, bool $readonly): Feralygon\Kit\Structure</code><br>
	 * <br>
	 * Parameters:<br>
	 * &nbsp; &#8226; &nbsp; <code><b>array $properties</b></code><br>
	 * &nbsp; &nbsp; &nbsp; The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * &nbsp; &#8226; &nbsp; <code><b>bool $readonly</b></code><br>
	 * &nbsp; &nbsp; &nbsp; Set the built instance as read-only.<br>
	 * <br>
	 * Return: <code><b>Feralygon\Kit\Structure</b></code><br>
	 * The built instance.</p>
	 * @throws \Feralygon\Kit\Structure\Exceptions\CoercionFailed
	 * @return static
	 * <p>The given value coerced into an instance.</p>
	 */
	final public static function coerce(
		$value, bool $clone = false, ?bool $readonly = null, ?callable $builder = null
	): Structure
	{
		//builder
		if (!isset($builder)) {
			$builder = static::getDefaultBuilder();
		}
		if (isset($builder)) {
			UCall::assert('builder', $builder, function (array $properties, bool $readonly): Structure {});
		}
		
		//coerce
		try {
			if (!isset($value) || is_array($value)) {
				return isset($builder)
					? UType::coerceObject($builder($value ?? [], $readonly ?? false), static::class)
					: new static($value ?? [], $readonly ?? false);
			} elseif (is_object($value) && $value instanceof Structure) {
				if ($clone || (isset($readonly) && $readonly !== $value->isReadonly())) {
					return new static($value->getAll(), $readonly ?? false);
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
				'structure' => static::class,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_BUILD_EXCEPTION,
				'error_message' => $exception->getMessage()
			]);
		}
		
		//throw
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'structure' => static::class,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
			'error_message' => "Only null, an instance or array of properties, " . 
				"given as \"name => value\" pairs, can be coerced into an instance."
		]);
	}
}
