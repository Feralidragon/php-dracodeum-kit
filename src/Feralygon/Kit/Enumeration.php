<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Enumeration\{
	Traits,
	Exceptions
};
use Feralygon\Kit\Utilities\Text as UText;
use Feralygon\Kit\Options\Text as TextOptions;
use Feralygon\Kit\Traits as KitTraits;

/**
 * This class is the base to be extended from when creating an enumeration.
 * 
 * An enumeration is defined by declaring its possible elements as public constants of the class.<br>
 * All enumeration values must be integers, floats or strings.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Enumerated_type
 * @see \Feralygon\Kit\Enumeration\Traits\Information
 */
abstract class Enumeration
{
	//Traits
	use KitTraits\NonInstantiable;
	use Traits\Information;
	
	
	
	//Public constants
	/**
	 * Define the enumeration elements as public constants here.
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
	 * Check if has a given element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element
	 * <p>The element to check for, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is checked only by its value.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the given element.</p>
	 */
	final public static function has($element) : bool
	{
		return static::hasValue($element) || static::hasName($element);
	}
	
	/**
	 * Check if has element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value
	 * <p>The value to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the element with the given value.</p>
	 */
	final public static function hasValue($value) : bool
	{
		return isset(static::getValuesNames()[(string)$value]);
	}
	
	/**
	 * Check if has element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the element with the given name.</p>
	 */
	final public static function hasName(string $name) : bool
	{
		return isset(static::getNamesValues()[$name]);
	}
	
	/**
	 * Get value from a given element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @return int|float|string
	 * <p>The value from the given element.</p>
	 */
	final public static function getValue($element)
	{
		return static::getNamesValues()[static::getName($element)];
	}
	
	/**
	 * Get name from a given element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\ElementNotFound
	 * @return string
	 * <p>The name from the given element.</p>
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
	 * Get name from the element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\ValueNotFound
	 * @return string
	 * <p>The name from the element with the given value.</p>
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
	 * Get value from the element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\NameNotFound
	 * @return int|float|string
	 * <p>The value from the element with the given name.</p>
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
	 * Evaluate a given value as an element value.
	 * 
	 * Only an element given as an integer, float or string can be evaluated into an element value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is a valid element and was sanitized into an element value.</p>
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
	 * Coerce a given value into an element value.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\ValueCoercionFailed
	 * @return int|float|string|null
	 * <p>The given value coerced into an element value.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceValue($value, bool $nullable = false)
	{
		//coerce
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
				'error_message' => "Only an enumeration element given as an integer, float or string " . 
					"can be coerced into an enumeration element value."
			]);
		} elseif ((is_string($value) && static::hasName($value)) || static::hasValue($value)) {
			return static::getValue($value);
		}
		
		//throw
		throw new Exceptions\ValueCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No such enumeration element has been found."
		]);
	}
	
	/**
	 * Evaluate a given value as an element name.
	 * 
	 * Only an element given as an integer, float or string can be evaluated into an element name.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is a valid element and was sanitized into an element name.</p>
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
	 * Coerce a given value into an element name.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element name.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\NameCoercionFailed
	 * @return string|null
	 * <p>The given value coerced into an element name.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceName($value, bool $nullable = false) : ?string
	{
		//coerce
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
				'error_message' => "Only an enumeration element given as an integer, float or string " . 
					"can be coerced into an enumeration element name."
			]);
		} elseif (is_string($value) && static::hasName($value)) {
			return $value;
		} elseif (static::hasValue($value)) {
			return static::getName($value);
		}
		
		//throw
		throw new Exceptions\NameCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No such enumeration element has been found."
		]);
	}
	
	/**
	 * Get label from a given element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The label from the given element.</p>
	 */
	final public static function getLabel($element, $text_options = null) : string
	{
		return static::getNameLabel(static::getName($element), $text_options);
	}
	
	/**
	 * Get label from the element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The label from the element with the given value.</p>
	 */
	final public static function getValueLabel($value, $text_options = null) : string
	{
		return static::getNameLabel(static::getValueName($value), $text_options);
	}
	
	/**
	 * Get label from the element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Enumeration\Exceptions\NameNotFound
	 * @return string
	 * <p>The label from the element with the given name.</p>
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
	 * Get description from a given element.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The description from the given element or <code>null</code> if none exists.</p>
	 */
	final public static function getDescription($element, $text_options = null) : ?string
	{
		return static::getNameDescription(static::getName($element), $text_options);
	}
	
	/**
	 * Get description from the element with a given value.
	 * 
	 * @since 1.0.0
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The description from the element with the given value or <code>null</code> if none exists.</p>
	 */
	final public static function getValueDescription($value, $text_options = null) : ?string
	{
		return static::getNameDescription(static::getValueName($value), $text_options);
	}
	
	/**
	 * Get description from the element with a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @param \Feralygon\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string|null
	 * <p>The description from the element with the given name or <code>null</code> if none exists.</p>
	 */
	final public static function getNameDescription(string $name, $text_options = null) : ?string
	{
		if (!static::hasName($name)) {
			throw new Exceptions\NameNotFound(['enumeration' => static::class, 'name' => $name]);
		}
		return static::retrieveDescription($name, TextOptions::coerce($text_options)) ?? null;
	}
	
	/**
	 * Get names.
	 * 
	 * @since 1.0.0
	 * @return string[]
	 * <p>The names.</p>
	 */
	final public static function getNames() : array
	{
		return array_values(self::getValuesNames());
	}
	
	/**
	 * Get values.
	 * 
	 * @since 1.0.0
	 * @return int[]|float[]|string[]
	 * <p>The values.</p>
	 */
	final public static function getValues() : array
	{
		return array_values(self::getNamesValues());
	}
	
	/**
	 * Get values names.
	 * 
	 * @since 1.0.0
	 * @return string[]
	 * <p>The values names, as <samp>value => name</samp> pairs.</p>
	 */
	final public static function getValuesNames() : array
	{
		if (!isset(self::$values_names[static::class])) {
			self::$values_names[static::class] = array_flip(array_map('strval', static::getNamesValues()));
		}
		return self::$values_names[static::class];
	}
	
	/**
	 * Get names values.
	 * 
	 * @since 1.0.0
	 * @return int[]|float[]|string[]
	 * <p>The names values, as <samp>name => value</samp> pairs.</p>
	 */
	final public static function getNamesValues() : array
	{
		if (!isset(self::$names_values[static::class])) {
			self::$names_values[static::class] = (new \ReflectionClass(static::class))->getConstants();
		}
		return self::$names_values[static::class];
	}
}
