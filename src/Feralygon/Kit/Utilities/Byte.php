<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\Utility;
use Feralygon\Kit\Utilities\Byte\{
	Options,
	Exceptions
};

/**
 * This utility implements a set of methods used to get information at the byte level.
 * 
 * @since 1.0.0
 */
final class Byte extends Utility
{
	//Private constants
	/** Multiples table. */
	private const MULTIPLES_TABLE = [[
		'bytes' => 1e18,
		'symbol' => 'EB',
		'symbol_alt' => 'E',
		'singular' => "exabyte",
		'plural' => "exabytes",
		'precision' => 1
	], [
		'bytes' => 1e15,
		'symbol' => 'PB',
		'symbol_alt' => 'P',
		'singular' => "petabyte",
		'plural' => "petabytes",
		'precision' => 1
	], [
		'bytes' => 1e12,
		'symbol' => 'TB',
		'symbol_alt' => 'T',
		'singular' => "terabyte",
		'plural' => "terabytes",
		'precision' => 1
	], [
		'bytes' => 1e9,
		'symbol' => 'GB',
		'symbol_alt' => 'G',
		'singular' => "gigabyte",
		'plural' => "gigabytes",
		'precision' => 1
	], [
		'bytes' => 1e6,
		'symbol' => 'MB',
		'symbol_alt' => 'M',
		'singular' => "megabyte",
		'plural' => "megabytes",
		'precision' => 2
	], [
		'bytes' => 1e3,
		'symbol' => 'kB',
		'symbol_alt' => 'k',
		'singular' => "kilobyte",
		'plural' => "kilobytes",
		'precision' => 2
	], [
		'bytes' => 1,
		'symbol' => 'B',
		'symbol_alt' => '',
		'singular' => "byte",
		'plural' => "bytes",
		'precision' => 0
	]];
	
	
	
	//Private static properties
	/** @var int[] */
	private static $multiples = [];
	
	
	
	//Final public static methods
	/**
	 * Get human-readable value from a given machine one.
	 * 
	 * The returning value represents the given one in a human-readable format and in bytes, 
	 * by rounding it to the nearest most significant byte multiple, as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <code>100</code> returns <samp>100 B</samp>, 
	 * or <samp>100 bytes</samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>255000</code> returns <samp>255 kB</samp>, 
	 * or <samp>255 kilobytes</samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>3752400</code> returns <samp>3.75 MB</samp>, 
	 * or <samp>3.75 megabytes</samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>47958383032</code> returns <samp>47.96 GB</samp>, 
	 * or <samp>47.96 gigabytes</samp> in long form.
	 * 
	 * @since 1.0.0
	 * @param int $value
	 * <p>The machine-readable value to get from, in bytes.</p>
	 * @param \Feralygon\Kit\Utilities\Byte\Options\Hvalue|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The human-readable value from the given machine one.</p>
	 */
	final public static function hvalue(int $value, $options = null): string
	{
		//initialize
		$options = Options\Hvalue::coerce($options);
		$precision = $options->precision;
		$sign = $value >= 0 ? '' : '-';
		$value = abs($value);
		$number = 0.0;
		$multiple_row = array_slice(self::MULTIPLES_TABLE, -1)[0];
		$min_multiple = $options->min_multiple;
		$max_multiple = $options->max_multiple;
		
		//process
		foreach (self::MULTIPLES_TABLE as $row) {
			if (isset($max_multiple) && $row['bytes'] > $max_multiple) {
				continue;
			} elseif ($value < $row['bytes'] && (!isset($min_multiple) || $row['bytes'] > $min_multiple)) {
				continue;
			}
			$number = round($value / $row['bytes'], $precision ?? $row['precision']);
			$multiple_row = $row;
			break;
		}
		
		//return
		return "{$sign}{$number} " . ($options->long
			? ($number === 1.0 ? $multiple_row['singular'] : $multiple_row['plural'])
			: $multiple_row['symbol']
		);
	}
	
	/**
	 * Get machine-readable value from a given human one.
	 * 
	 * The returning value represents the given one in a machine-readable format and in bytes, 
	 * by converting it as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <samp>100 B</samp> or <samp>100</samp> 
	 * or <samp>100 bytes</samp> returns <code>100</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>255 kB</samp> or <samp>255k</samp> 
	 * or <samp>255 kilobytes</samp> returns <code>255000</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>3.75 MB</samp> or <samp>3.75M</samp> 
	 * or <samp>3.75 megabytes</samp> returns <code>3750000</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>47.96 GB</samp> or <samp>47.96G</samp> 
	 * or <samp>47.96 gigabytes</samp> returns <code>47960000000</code>.
	 * 
	 * @since 1.0.0
	 * @param string $value
	 * <p>The human-readable value to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Byte\Exceptions\Mvalue\InvalidValue
	 * @return int|null
	 * <p>The machine-readable value in bytes from the given human one.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be got.</p>
	 */
	final public static function mvalue(string $value, bool $no_throw = false): ?int
	{
		//parse
		$pattern = '/^\s*(?P<sign>[\-+])?(?P<number>\d+(?:[\.,]\d+)?)\s*(?P<multiple>[^\s]+)?\s*$/';
		if (!preg_match($pattern, $value, $matches)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Mvalue\InvalidValue(['value' => $value]);
		}
		$sign = $matches['sign'];
		$number = (float)str_replace(',', '.', $matches['number']);
		$multiple = $matches['multiple'] ?? '';
		
		//calculate
		if (!self::evaluateMultiple($multiple)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Mvalue\InvalidValue(['value' => $value]);
		}
		$number *= $multiple;
		if ($sign === '-') {
			$number *= -1;
		}
		return $number;
	}
	
	/**
	 * Evaluate a given value as a size.
	 * 
	 * Only the following types and formats can be evaluated into a size:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a size.</p>
	 */
	final public static function evaluateSize(&$value, bool $nullable = false): bool
	{
		try {
			$value = self::coerceSize($value, $nullable);
		} catch (Exceptions\SizeCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a size.
	 * 
	 * Only the following types and formats can be coerced into a size:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, 
	 * such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Utilities\Byte\Exceptions\SizeCoercionFailed
	 * @return int|null
	 * <p>The given value coerced into a size.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceSize($value, bool $nullable = false): ?int
	{
		//coerce
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\SizeCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\SizeCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (Type::evaluateInteger($value)) {
			return $value;
		} else {
			$v = self::mvalue($value, true);
			if (isset($v)) {
				return $v;
			}
		}
		
		//throw
		throw new Exceptions\SizeCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\SizeCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a size:\n" . 
				" - an integer, such as: 123000 for 123000;\n" . 
				" - a whole float, such as: 123000.0 for 123000;\n" . 
				" - a numeric string, such as: \"123000\" for 123000;\n" . 
				" - a numeric string in exponential notation, such as: \"123e3\" or \"123E3\" for 123000;\n" . 
				" - a numeric string in octal notation, such as: \"0360170\" for 123000;\n" . 
				" - a numeric string in hexadecimal notation, such as: \"0x1e078\" or \"0x1E078\" for 123000;\n" . 
				" - a human-readable numeric string, such as: \"123k\" or \"123 thousand\" for 123000;\n" . 
				" - a human-readable numeric string in bytes, such as: \"123kB\" or \"123 kilobytes\" for 123000."
		]);
	}
	
	/**
	 * Evaluate a given value as a multiple.
	 * 
	 * Only the following types and formats can be evaluated into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as a power of 10, 
	 * such as: <code>1000</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; an SI symbol string, 
	 * such as: <code>"kB"</code> or <code>"k"</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; an SI name string in English, 
	 * such as: <code>"kilobyte"</code> or <code>"kilobytes"</code> for kilobytes.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a multiple.</p>
	 */
	final public static function evaluateMultiple(&$value, bool $nullable = false): bool
	{
		try {
			$value = self::coerceMultiple($value, $nullable);
		} catch (Exceptions\MultipleCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a multiple.
	 * 
	 * Only the following types and formats can be coerced into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; an integer or float as a power of 10, 
	 * such as: <code>1000</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; an SI symbol string, 
	 * such as: <code>"kB"</code> or <code>"k"</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; an SI name string in English, 
	 * such as: <code>"kilobyte"</code> or <code>"kilobytes"</code> for kilobytes.
	 * 
	 * @since 1.0.0
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Utilities\Byte\Exceptions\MultipleCoercionFailed
	 * @return int|null
	 * <p>The given value coerced into a multiple.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceMultiple($value, bool $nullable = false): ?int
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\MultipleCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//multiples
		if (empty(self::$multiples)) {
			foreach (self::MULTIPLES_TABLE as $row) {
				foreach (['bytes', 'symbol', 'symbol_alt', 'singular', 'plural'] as $column) {
					self::$multiples[(string)$row[$column]] = (int)$row['bytes'];
				}
			}
		}
		
		//validate
		if (!is_int($value) && !is_float($value) && !is_string($value)) {
			throw new Exceptions\MultipleCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a multiple given as an integer, float or string is allowed."
			]);
		}
		
		//coerce
		$value = (string)$value;
		if (!isset(self::$multiples[$value])) {
			throw new Exceptions\MultipleCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\MultipleCoercionFailed::ERROR_CODE_INVALID,
				'error_message' => "Only the following types and formats can be coerced into a multiple:\n" . 
					" - an integer or float as a power of 10, such as 1000 for kilobytes;\n" . 
					" - an SI symbol string, such as \"kB\" or \"k\" for kilobytes;\n" . 
					" - an SI name string in English, such as \"kilobyte\" or \"kilobytes\" for kilobytes."
			]);
		}
		return self::$multiples[$value];
	}
}
