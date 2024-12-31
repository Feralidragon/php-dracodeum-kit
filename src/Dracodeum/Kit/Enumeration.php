<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;
use Dracodeum\Kit\Enumeration\{
	Traits,
	Exceptions
};
use Dracodeum\Kit\Utilities\Text as UText;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Traits as KTraits;

/**
 * This class is the base to be extended from when creating an enumeration.
 * 
 * An enumeration is defined by declaring its possible elements as public constants of the class.<br>
 * All enumeration values must be integers, floats or strings.
 * 
 * @see https://en.wikipedia.org/wiki/Enumerated_type
 * @see \Dracodeum\Kit\Enumeration\Traits\Information
 */
abstract class Enumeration implements IUninstantiable
{
	//Traits
	use KTraits\Uninstantiable;
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
	 * @param int|float|string $element
	 * <p>The element to check for, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is checked only by its value.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the given element.</p>
	 */
	final public static function has($element): bool
	{
		return static::hasValue($element) || static::hasName($element);
	}
	
	/**
	 * Check if has element with a given value.
	 * 
	 * @param int|float|string $value
	 * <p>The value to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the element with the given value.</p>
	 */
	final public static function hasValue($value): bool
	{
		return isset(static::getValuesNames()[(string)$value]);
	}
	
	/**
	 * Check if has element with a given name.
	 * 
	 * @param string $name
	 * <p>The name to check with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has the element with the given name.</p>
	 */
	final public static function hasName(string $name): bool
	{
		return isset(static::getNamesValues()[$name]);
	}
	
	/**
	 * Get value from a given element.
	 * 
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ElementNotFound
	 * @return int|float|string|null
	 * <p>The value from the given element.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getValue($element, bool $no_throw = false)
	{
		$name = static::getName($element, $no_throw);
		return isset($name) ? static::getNamesValues()[$name] : null;
	}
	
	/**
	 * Get name from a given element.
	 * 
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ElementNotFound
	 * @return string|null
	 * <p>The name from the given element.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getName($element, bool $no_throw = false): ?string
	{
		if (static::hasValue($element)) {
			return static::getValuesNames()[(string)$element];
		} elseif (static::hasName($element)) {
			return $element;
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\ElementNotFound([static::class, $element]);
	}
	
	/**
	 * Get name from the element with a given value.
	 * 
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ValueNotFound
	 * @return string|null
	 * <p>The name from the element with the given value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getValueName($value, bool $no_throw = false): ?string
	{
		$name = static::getValuesNames()[(string)$value] ?? null;
		if (!isset($name)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ValueNotFound([static::class, $value]);
		}
		return $name;
	}
	
	/**
	 * Get value from the element with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\NameNotFound
	 * @return int|float|string|null
	 * <p>The value from the element with the given name.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getNameValue(string $name, bool $no_throw = false)
	{
		$value = static::getNamesValues()[$name] ?? null;
		if (!isset($value)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\NameNotFound([static::class, $name]);
		}
		return $value;
	}
	
	/**
	 * Evaluate a given value as an element value.
	 * 
	 * Only an element given as an integer, float or string can be evaluated into an element value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is a valid element and was sanitized into an element value.</p>
	 */
	final public static function evaluateValue(&$value, bool $nullable = false): bool
	{
		return self::processValueCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an element value.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element value.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ValueCoercionFailed
	 * @return int|float|string|null
	 * <p>The given value coerced into an element value.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceValue($value, bool $nullable = false)
	{
		self::processValueCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an element value.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ValueCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an element value.</p>
	 */
	final public static function processValueCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\ValueCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ValueCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only an enumeration element given as an integer, float or string " . 
					"can be coerced into an enumeration element value."
			]);
		} elseif ((is_string($value) && static::hasName($value)) || static::hasValue($value)) {
			$value = static::getValue($value);
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\ValueCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\ValueCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No enumeration element found."
		]);
	}
	
	/**
	 * Evaluate a given value as an element name.
	 * 
	 * Only an element given as an integer, float or string can be evaluated into an element name.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is a valid element and was sanitized into an element name.</p>
	 */
	final public static function evaluateName(&$value, bool $nullable = false): bool
	{
		return self::processNameCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an element name.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element name.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\NameCoercionFailed
	 * @return string|null
	 * <p>The given value coerced into an element name.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceName($value, bool $nullable = false): ?string
	{
		self::processNameCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an element name.
	 * 
	 * Only an element given as an integer, float or string can be coerced into an element name.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\NameCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an element name.</p>
	 */
	final public static function processNameCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\NameCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\NameCoercionFailed([
				'enumeration' => static::class,
				'value' => $value,
				'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only an enumeration element given as an integer, float or string " . 
					"can be coerced into an enumeration element name."
			]);
		} elseif (is_string($value) && static::hasName($value)) {
			return true;
		} elseif (static::hasValue($value)) {
			$value = static::getName($value);
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\NameCoercionFailed([
			'enumeration' => static::class,
			'value' => $value,
			'error_code' => Exceptions\NameCoercionFailed::ERROR_CODE_NOT_FOUND,
			'error_message' => "No enumeration element found."
		]);
	}
	
	/**
	 * Get label from a given element.
	 * 
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ElementNotFound
	 * @return string|null
	 * <p>The label from the given element.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getLabel($element, $text_options = null, bool $no_throw = false): ?string
	{
		$name = static::getName($element, $no_throw);
		return isset($name) ? static::getNameLabel($name, $text_options) : null;
	}
	
	/**
	 * Get label from the element with a given value.
	 * 
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ValueNotFound
	 * @return string|null
	 * <p>The label from the element with the given value.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getValueLabel($value, $text_options = null, bool $no_throw = false): ?string
	{
		$name = static::getValueName($value, $no_throw);
		return isset($name) ? static::getNameLabel($name, $text_options) : null;
	}
	
	/**
	 * Get label from the element with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\NameNotFound
	 * @return string|null
	 * <p>The label from the element with the given name.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function getNameLabel(string $name, $text_options = null, bool $no_throw = false): ?string
	{
		if (!static::hasName($name)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\NameNotFound([static::class, $name]);
		}
		return static::returnLabel($name, TextOptions::coerce($text_options))
			?? UText::unslugify(strtolower($name), UText::UNSLUG_CAPITALIZE_ALL);
	}
	
	/**
	 * Get description from a given element.
	 * 
	 * @param int|float|string $element
	 * <p>The element to get from, by value or name.<br>
	 * If any existing value matches an existing name, then the given element is retrieved only by its value.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ElementNotFound
	 * @return string|null
	 * <p>The description from the given element or <code>null</code> if none is set.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if it was not found.</p>
	 */
	final public static function getDescription($element, $text_options = null, bool $no_throw = false): ?string
	{
		$name = static::getName($element, $no_throw);
		return isset($name) ? static::getNameDescription($name, $text_options) : null;
	}
	
	/**
	 * Get description from the element with a given value.
	 * 
	 * @param int|float|string $value
	 * <p>The value to get from.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\ValueNotFound
	 * @return string|null
	 * <p>The description from the element with the given value or <code>null</code> if none is set.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if it was not found.</p>
	 */
	final public static function getValueDescription($value, $text_options = null, bool $no_throw = false): ?string
	{
		$name = static::getValueName($value, $no_throw);
		return isset($name) ? static::getNameDescription($name, $text_options) : null;
	}
	
	/**
	 * Get description from the element with a given name.
	 * 
	 * @param string $name
	 * <p>The name to get from.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Enumeration\Exceptions\NameNotFound
	 * @return string|null
	 * <p>The description from the element with the given name or <code>null</code> if none is set.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> may also be returned if it was not found.</p>
	 */
	final public static function getNameDescription(
		string $name, $text_options = null, bool $no_throw = false
	): ?string
	{
		if (!static::hasName($name)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\NameNotFound([static::class, $name]);
		}
		return static::returnDescription($name, TextOptions::coerce($text_options)) ?? null;
	}
	
	/**
	 * Get names.
	 * 
	 * @return string[]
	 * <p>The names.</p>
	 */
	final public static function getNames(): array
	{
		return array_values(self::getValuesNames());
	}
	
	/**
	 * Get values.
	 * 
	 * @return int[]|float[]|string[]
	 * <p>The values.</p>
	 */
	final public static function getValues(): array
	{
		return array_values(self::getNamesValues());
	}
	
	/**
	 * Get values names.
	 * 
	 * @return string[]
	 * <p>The values names, as a set of <samp>value => name</samp> pairs.</p>
	 */
	final public static function getValuesNames(): array
	{
		if (!isset(self::$values_names[static::class])) {
			self::$values_names[static::class] = array_flip(array_map('strval', static::getNamesValues()));
		}
		return self::$values_names[static::class];
	}
	
	/**
	 * Get names values.
	 * 
	 * @return int[]|float[]|string[]
	 * <p>The names values, as a set of <samp>name => value</samp> pairs.</p>
	 */
	final public static function getNamesValues(): array
	{
		if (!isset(self::$names_values[static::class])) {
			self::$names_values[static::class] = (new \ReflectionClass(static::class))->getConstants();
		}
		return self::$names_values[static::class];
	}
}
