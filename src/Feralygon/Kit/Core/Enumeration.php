<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

use Feralygon\Kit\Core\Enumeration\{
	Traits,
	Exceptions
};
use Feralygon\Kit\Core\Utilities\Text as UText;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Traits\NonInstantiable as TNonInstantiable;

/**
 * Core enumeration class.
 * 
 * This class is the base to be extended from when creating an enumeration.<br>
 * <br>
 * An enumeration is defined by declaring its possible elements as public constants of the class.<br>
 * All enumerated values must be integers, floats or strings.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Enumerated_type
 * @see \Feralygon\Kit\Core\Enumeration\Traits\Information
 */
abstract class Enumeration
{
	//Traits
	use TNonInstantiable;
	use Traits\Information;
	
	
	
	//Public constants
	/**
	 * Define the enumerated elements as public constants here.
	 * Example:
	 * 	public const ELEMENT1 = 1;
	 * 	public const ELEMENT2 = 2;
	 *  ...
	 */
	
	
	
	//Private static properties
	/** @var int[]|float[]|string[] */
	private static $names_values = [];
	
	/** @var string[] */
	private static $values_names = [];
	
	
	
	//Final public static methods
	/**
	 * Check if has a given enumerated element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element <p>The enumerated element to check for, by value or name.<br>
	 * If any existing value matches an existing name, the given element is checked only by its value.</p>
	 * @return bool <p>Boolean <code>true</code> if has the given enumerated element.</p>
	 */
	final public static function has($element) : bool
	{
		return static::hasValue($element) || static::hasName($element);
	}
	
	/**
	 * Check if has an enumerated element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value <p>The enumerated element value to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if has the enumerated element with the given value.</p>
	 */
	final public static function hasValue($value) : bool
	{
		return isset(static::getValuesNames()[(string)$value]);
	}
	
	/**
	 * Check if has an enumerated element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to check for.</p>
	 * @return bool <p>Boolean <code>true</code> if has the enumerated element with the given name.</p>
	 */
	final public static function hasName(string $name) : bool
	{
		return isset(static::getNamesValues()[$name]);
	}
	
	/**
	 * Get value from a given enumerated element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element <p>The enumerated element to get for, by value or name.<br>
	 * If any existing value matches an existing name, the given element is retrieved only by its value.</p>
	 * @return int|float|string <p>The value from the given enumerated element.</p>
	 */
	final public static function getValue($element)
	{
		return static::getNamesValues()[static::getName($element)];
	}
	
	/**
	 * Get name from a given enumerated element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element <p>The enumerated element to get for, by value or name.<br>
	 * If any existing value matches an existing name, the given element is retrieved only by its value.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\ElementNotFound
	 * @return string <p>The name from the given enumerated element.</p>
	 */
	final public static function getName($element) : string
	{
		if (static::hasValue($element)) {
			return static::getValuesNames()[(string)$element];
		} elseif (static::hasName($element)) {
			return (string)$element;
		}
		throw new Exceptions\ElementNotFound(['enumeration' => static::class, 'element' => $element]);
	}
	
	/**
	 * Get name from the enumerated element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value <p>The enumerated element value to get for.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\ValueNotFound
	 * @return string <p>The name from the enumerated element with the given value.</p>
	 */
	final public static function getValueName($value) : string
	{
		$name = static::getValuesNames()[(string)$value] ?? null;
		if (!isset($name)) {
			throw new Exceptions\ValueNotFound(['enumeration' => static::class, 'value' => $value]);
		}
		return $name;
	}
	
	/**
	 * Get value from the enumerated element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to get for.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\NameNotFound
	 * @return int|float|string <p>The value from the enumerated element with the given name.</p>
	 */
	final public static function getNameValue(string $name)
	{
		$value = static::getNamesValues()[$name] ?? null;
		if (!isset($value)) {
			throw new Exceptions\NameNotFound(['enumeration' => static::class, 'name' => $name]);
		}
		return $value;
	}
	
	/**
	 * Evaluate a given value as an enumerated element value.
	 * 
	 * Only an enumerated element given as an integer, float or string can be evaluated 
	 * into an enumerated element value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is a valid enumerated element 
	 * and was sanitized into an enumerated element value.</p>
	 */
	final public static function evaluateValue(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceValue($value, $nullable);
		} catch (Exceptions\ValueCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an enumerated element value.
	 * 
	 * Only an enumerated element given as an integer, float or string can be coerced into an enumerated element value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\ValueCoercionFailed
	 * @return int|float|string|null <p>The given value coerced into an enumerated element value.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceValue($value, bool $nullable = false)
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\ValueCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_int($value) && !is_float($value) && !is_string($value)) {
			throw new Exceptions\ValueCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only an enumerated element given as an integer, float or string " . 
					"can be coerced into an enumerated element value."
			]);
		} elseif ((is_string($value) && static::hasName($value)) || static::hasValue($value)) {
			return static::getValue($value);
		}
		throw new Exceptions\ValueCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No such enumerated element has been found."
		]);
	}
	
	/**
	 * Evaluate a given value as an enumerated element name.
	 * 
	 * Only an enumerated element given as an integer, float or string can be evaluated into an enumerated element name.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is a valid enumerated element 
	 * and was sanitized into an enumerated element name.</p>
	 */
	final public static function evaluateName(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceName($value, $nullable);
		} catch (Exceptions\NameCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an enumerated element name.
	 * 
	 * Only an enumerated element given as an integer, float or string can be coerced into an enumerated element name.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\NameCoercionFailed
	 * @return string|null <p>The given value coerced into an enumerated element name.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceName($value, bool $nullable = false) : ?string
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\NameCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_int($value) && !is_float($value) && !is_string($value)) {
			throw new Exceptions\NameCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only an enumerated element given as an integer, float or string " . 
					"can be coerced into an enumerated element name."
			]);
		} elseif (is_string($value) && static::hasName($value)) {
			return $value;
		} elseif (static::hasValue($value)) {
			return static::getName($value);
		}
		throw new Exceptions\NameCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No such enumerated element has been found."
		]);
	}
	
	/**
	 * Get label from a given enumerated element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element <p>The enumerated element to get for, by value or name.<br>
	 * If any existing value matches an existing name, the given element is retrieved only by its value.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The label from the given enumerated element.</p>
	 */
	final public static function getLabel($element, $text_options = null) : string
	{
		return static::getNameLabel(static::getName($element), $text_options);
	}
	
	/**
	 * Get label from the enumerated element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value <p>The enumerated element value to get for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The label from the enumerated element with the given value.</p>
	 */
	final public static function getValueLabel($value, $text_options = null) : string
	{
		return static::getNameLabel(static::getValueName($value), $text_options);
	}
	
	/**
	 * Get label from the enumerated element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to get for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Enumeration\Exceptions\NameNotFound
	 * @return string <p>The label from the enumerated element with the given name.</p>
	 */
	final public static function getNameLabel(string $name, $text_options = null) : string
	{
		if (!static::hasName($name)) {
			throw new Exceptions\NameNotFound(['enumeration' => static::class, 'name' => $name]);
		}
		return static::retrieveLabel($name, TextOptions::coerce($text_options))
			?? UText::unslugify(strtolower($name), UText::UNSLUG_CAPITALIZE_ALL);
	}
	
	/**
	 * Get description from a given enumerated element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element <p>The enumerated element to get for, by value or name.<br>
	 * If any existing value matches an existing name, the given element is retrieved only by its value.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The description from the given enumerated element or <code>null</code> if none exists.</p>
	 */
	final public static function getDescription($element, $text_options = null) : ?string
	{
		return static::getNameDescription(static::getName($element), $text_options);
	}
	
	/**
	 * Get description from the enumerated element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value <p>The enumerated element value to get for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The description from the enumerated element with the given value 
	 * or <code>null</code> if none exists.</p>
	 */
	final public static function getValueDescription($value, $text_options = null) : ?string
	{
		return static::getNameDescription(static::getValueName($value), $text_options);
	}
	
	/**
	 * Get description from the enumerated element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name <p>The enumerated element name to get for.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] 
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null <p>The description from the enumerated element with the given name 
	 * or <code>null</code> if none exists.</p>
	 */
	final public static function getNameDescription(string $name, $text_options = null) : ?string
	{
		if (!static::hasName($name)) {
			throw new Exceptions\NameNotFound(['enumeration' => static::class, 'name' => $name]);
		}
		return static::retrieveDescription($name, TextOptions::coerce($text_options)) ?? null;
	}
	
	/**
	 * Get the enumerated values names.
	 * 
	 * @since 1.0.0
	 * @return string[] <p>The enumerated values names, as <samp>value => name</samp> pairs.</p>
	 */
	final public static function getValuesNames() : array
	{
		if (!isset(self::$values_names[static::class])) {
			self::$values_names[static::class] = array_flip(array_map('strval', static::getNamesValues()));
		}
		return self::$values_names[static::class];
	}
	
	/**
	 * Get the enumerated names values.
	 * 
	 * @since 1.0.0
	 * @return int[]|float[]|string[] <p>The enumerated names values, as <samp>name => value</samp> pairs.</p>
	 */
	final public static function getNamesValues() : array
	{
		if (!isset(self::$names_values[static::class])) {
			self::$names_values[static::class] = (new \ReflectionClass(static::class))->getConstants();
		}
		return self::$names_values[static::class];
	}
}
