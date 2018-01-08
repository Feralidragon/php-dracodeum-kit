<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Utilities\Byte\{
	Options,
	Exceptions
};

/**
 * Core byte utility class.
 * 
 * This utility implements a set of methods used to retrieve information at the byte level.
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
	 * Retrieve human-readable value from a given machine one.
	 * 
	 * The returning value represents the given one in a human-readable format and in bytes, 
	 * by rounding it to the nearest most significant byte multiple, as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <code>100</code> returns <samp><i>100 B</i></samp>, or <samp><i>100 bytes</i></samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>255000</code> returns <samp><i>255 kB</i></samp>, or <samp><i>255 kilobytes</i></samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>3752400</code> returns <samp><i>3.75 MB</i></samp>, or <samp><i>3.75 megabytes</i></samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>47958383032</code> returns <samp><i>47.96 GB</i></samp>, or <samp><i>47.96 gigabytes</i></samp> in long form.
	 * 
	 * @since 1.0.0
	 * @param int $value <p>The machine-readable value to retrieve from, in bytes.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Byte\Options\Hvalue|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string <p>The human-readable value from the given machine one.</p>
	 */
	final public static function hvalue(int $value, $options = null) : string
	{
		//initialize
		$options = Options\Hvalue::load($options);
		$precision = $options->precision;
		$sign = $value >= 0 ? '' : '-';
		$value = abs($value);
		$number = 0.0;
		$multiple_row = array_slice(self::MULTIPLES_TABLE, -1)[0];
		
		//process
		foreach (self::MULTIPLES_TABLE as $row) {
			if (isset($options->max_multiple) && $row['bytes'] > $options->max_multiple) {
				continue;
			} elseif ($value < $row['bytes'] && (!isset($options->min_multiple) || $row['bytes'] > $options->min_multiple)) {
				continue;
			}
			$number = round($value / $row['bytes'], $precision ?? $row['precision']);
			$multiple_row = $row;
			break;
		}
		
		//return
		return "{$sign}{$number} " . ($options->long ? ($number === 1.0 ? $multiple_row['singular'] : $multiple_row['plural']) : $multiple_row['symbol']);
	}
	
	/**
	 * Retrieve machine-readable value from a given human one.
	 * 
	 * The returning value represents the given one in a machine-readable format and in bytes, 
	 * by converting it as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <code>100 B</code> or <code>100</code> or <code>100 bytes</code> returns <samp><i>100</i></samp>.<br>
	 * &nbsp; &#8226; &nbsp; <code>255 kB</code> or <code>255k</code> or <code>255 kilobytes</code> returns <samp><i>255000</i></samp>.<br>
	 * &nbsp; &#8226; &nbsp; <code>3.75 MB</code> or <code>3.75M</code> or <code>3.75 megabytes</code> returns <samp><i>3750000</i></samp>.<br>
	 * &nbsp; &#8226; &nbsp; <code>47.96 GB</code> or <code>47.96G</code> or <code>47.96 gigabytes</code> returns <samp><i>47960000000</i></samp>.
	 * 
	 * @since 1.0.0
	 * @param string $value <p>The human-readable value to retrieve from.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Byte\Exceptions\MvalueInvalidValue
	 * @return int <p>The machine-readable value in bytes from the given human one.</p>
	 */
	final public static function mvalue(string $value) : int
	{
		//parse
		if (!preg_match('/^\s*([\-+])?(\d+([\.,]\d+)?)\s*([^\s]+)?\s*$/', $value, $matches)) {
			throw new Exceptions\MvalueInvalidValue(['value' => $value]);
		}
		$sign = $matches[1];
		$number = (float)str_replace(',', '.', $matches[2]);
		$multiple = $matches[4] ?? '';
		
		//calculate
		if (!self::evaluateMultiple($multiple)) {
			throw new Exceptions\MvalueInvalidValue(['value' => $value]);
		}
		$number *= $multiple;
		if ($sign === '-') {
			$number *= -1;
		}
		return (int)$number;
	}
	
	/**
	 * Evaluate a given value as a multiple.
	 * 
	 * Only the following types and formats can be evaluated into multiples:<br>
	 * &nbsp; &#8226; &nbsp; integers as powers of 10, such as: <code>1000</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; SI symbols, such as: <code>kB</code> or <code>k</code> for kilobytes;<br>
	 * &nbsp; &#8226; &nbsp; SI names in English, such as: <code>kilobyte</code> or <code>kilobytes</code> for kilobytes.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given value is successfully evaluated into a multiple.</p>
	 */
	final public static function evaluateMultiple(&$value, bool $nullable = false) : bool
	{
		//multiples
		if (empty(self::$multiples)) {
			foreach (self::MULTIPLES_TABLE as $row) {
				foreach (['bytes', 'symbol', 'symbol_alt', 'singular', 'plural'] as $column) {
					self::$multiples[(string)$row[$column]] = (int)$row['bytes'];
				}
			}
		}
	
		//evaluate
		if (!isset($value)) {
			return $nullable;
		} elseif (!is_scalar($value) || is_bool($value) || !isset(self::$multiples[(string)$value])) {
			return false;
		}
		$value = self::$multiples[(string)$value];
		return true;
	}
}
