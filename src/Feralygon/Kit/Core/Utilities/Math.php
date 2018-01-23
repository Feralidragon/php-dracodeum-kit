<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Utilities\Math\{
	Options,
	Exceptions
};

/**
 * Core math utility class.
 * 
 * This utility implements a set of methods used to perform calculations and generation of numbers.
 * 
 * @since 1.0.0
 */
final class Math extends Utility
{
	//Private constants
	/** Multiples table. */
	private const MULTIPLES_TABLE = [[
		'number' => 1e15,
		'symbol' => 'Q',
		'label' => "quadrillion",
		'precision' => 1
	], [
		'number' => 1e12,
		'symbol' => 'T',
		'label' => "trillion",
		'precision' => 1
	], [
		'number' => 1e9,
		'symbol' => 'B',
		'label' => "billion",
		'precision' => 1
	], [
		'number' => 1e6,
		'symbol' => 'M',
		'label' => "million",
		'precision' => 2
	], [
		'number' => 1e3,
		'symbol' => 'k',
		'label' => "thousand",
		'precision' => 2
	], [
		'number' => 1,
		'symbol' => '',
		'label' => "",
		'precision' => 2
	]];
	
	
	
	//Private static properties
	/** @var int[] */
	private static $multiples = [];
	
	
	
	//Final public static methods
	/**
	 * Generate a random integer number.
	 * 
	 * The returning number is generated by using the PHP core <code>mt_rand</code> function, 
	 * which uses the Mersenne Twister algorithm.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.mt-rand.php
	 * @param int $maximum [default = 1] <p>The maximum integer number to generate with (inclusive).</p>
	 * @param int $minimum [default = 0] <p>The minimum integer number to generate with (inclusive).</p>
	 * @param int|null $seed [default = null] <p>The seed value to generate with.<br>
	 * If not set, an internally generated seed is used.</p>
	 * @return int <p>The generated random integer number.</p>
	 */
	final public static function random(int $maximum = 1, int $minimum = 0, ?int $seed = null) : int
	{
		mt_srand($seed ?? (int)(microtime(true) * 1e6));
		return mt_rand($minimum, $maximum);
	}
	
	/**
	 * Generate a random float number.
	 * 
	 * The returning number is generated by using the PHP core <code>mt_rand</code> function, 
	 * which uses the Mersenne Twister algorithm.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.mt-rand.php
	 * @param float $maximum [default = 1.0] <p>The maximum float number to generate with (inclusive).</p>
	 * @param float $minimum [default = 0.0] <p>The minimum float number to generate with (inclusive).</p>
	 * @param int|null $seed [default = null] <p>The seed value to generate with.<br>
	 * If not set, an internally generated seed is used.</p>
	 * @return float <p>The generated random float number.</p>
	 */
	final public static function frandom(float $maximum = 1.0, float $minimum = 0.0, ?int $seed = null) : float
	{
		mt_srand($seed ?? (int)(microtime(true) * 1e6));
		return mt_rand() / mt_getrandmax() * ($maximum - $minimum) + $minimum;
	}
	
	/**
	 * Get a random integer or string value from a given set of weighted values.
	 * 
	 * The returning value is retrieved as one of the keys from the <var>$values_weights</var> parameter, 
	 * which assigns each value to a weight as <samp>value => weight</samp> pairs, hence only an integer or string value can be returned.<br>
	 * <br>
	 * The weights represent a bias towards each specific value and should always be numeric, 
	 * whereas the greater the weight relative the other values, the greater is the chance for its value to be returned.<br>
	 * <br>
	 * The randomization uses the PHP core <code>mt_rand</code> function, which uses the Mersenne Twister algorithm.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.mt-rand.php
	 * @param int[]|float[] $values_weights <p>The values weights to get from, as <samp>value => weight</samp> pairs.<br>
	 * Each weight must be greater than or equal to <code>0</code>.</p>
	 * @param int|null $seed [default = null] <p>The seed value to generate with.<br>
	 * If not set, an internally generated seed is used.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Math\Exceptions\WrandomInvalidValueWeight
	 * @return int|string|null <p>A random integer or string value from the given set of weighted values or <code>null</code> if the given set is empty.</p>
	 */
	final public static function wrandom(array $values_weights, ?int $seed = null)
	{
		//initialize
		foreach ($values_weights as $value => &$weight) {
			if (!is_numeric($weight) || $weight < 0) {
				throw new Exceptions\WrandomInvalidValueWeight(['value' => $value, 'weight' => $weight]);
			} elseif (empty($weight)) {
				unset($values_weights[$value]);
				continue;
			}
			$weight = (float)$weight;
		}
		unset($weight);
		if (empty($values_weights)) {
			return null;
		}
		
		//weights
		$max_weight = $min_weight = min($values_weights);
		foreach ($values_weights as $value => $weight) {
			$values_weights[$value] = $max_weight;
			$max_weight += $weight;
		}
		
		//randomize
		$w = self::frandom($max_weight, $min_weight, $seed);
		foreach (array_reverse($values_weights, true) as $value => $weight) {
			if ($w >= $weight) {
				return $value;
			}
		}
		return null;
	}
	
	/**
	 * Retrieve human-readable number from a given machine one.
	 * 
	 * The returning number represents the given one in a human-readable format, 
	 * by rounding it to the nearest most significant multiple, as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <code>150</code> returns <samp>150</samp>.<br>
	 * &nbsp; &#8226; &nbsp; <code>84290</code> returns <samp>84.29K</samp>, or <samp>84.29 thousand</samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>285000000</code> returns <samp>285M</samp>, or <samp>285 million</samp> in long form.<br>
	 * &nbsp; &#8226; &nbsp; <code>5789482000</code> returns <samp>5.8B</samp>, or <samp>5.8 billion</samp> in long form.<br>
	 * <br>
	 * The short numeric scale is the one used, therefore 1 billion is considered to be 1e9 (1000000000) and not 1e12 (1000000000000).
	 * 
	 * @since 1.0.0
	 * @param int $number <p>The machine-readable number to retrieve from.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Math\Options\Hnumber|array|null $options [default = null] <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The human-readable number from the given machine one.</p>
	 */
	final public static function hnumber(int $number, $text_options = null, $options = null) : string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		$options = Options\Hnumber::coerce($options);
		$precision = $options->precision;
		$negative = $number < 0;
		$number = abs($number);
		$multiple_row = array_slice(self::MULTIPLES_TABLE, -1)[0];
		
		//process
		$n = 0.0;
		foreach (self::MULTIPLES_TABLE as $row) {
			if (isset($options->max_multiple) && $row['number'] > $options->max_multiple) {
				continue;
			} elseif ($number < $row['number'] && (!isset($options->min_multiple) || $row['number'] > $options->min_multiple)) {
				continue;
			}
			$n = round($number / $row['number'], $precision ?? $row['precision']);
			$multiple_row = $row;
			break;
		}
		if ($negative) {
			$n = -$n;
		}
		
		//return
		if ($options->long) {
			switch ($multiple_row['symbol']) {
				case 'k':
					/**
					 * @description Human-readable number scaled in thousands.
					 * @placeholder number The number in thousands.
					 * @example 3 thousand
					 */
					return Text::plocalize("{{number}} thousand", "{{number}} thousand", $n, 'number', self::class, $text_options);
				case 'M':
					/**
					 * @description Human-readable number scaled in millions.
					 * @placeholder number The number in millions.
					 * @example 3 million
					 */
					return Text::plocalize("{{number}} million", "{{number}} million", $n, 'number', self::class, $text_options);
				case 'B':
					/**
					 * @description Human-readable number scaled in billions.
					 * @placeholder number The number in billions.
					 * @example 3 billion
					 */
					return Text::plocalize("{{number}} billion", "{{number}} billion", $n, 'number', self::class, $text_options);
				case 'T':
					/**
					 * @description Human-readable number scaled in trillions.
					 * @placeholder number The number in trillions.
					 * @example 3 trillion
					 */
					return Text::plocalize("{{number}} trillion", "{{number}} trillion", $n, 'number', self::class, $text_options);
				case 'Q':
					/**
					 * @description Human-readable number scaled in quadrillions.
					 * @placeholder number The number in quadrillions.
					 * @example 3 quadrillion
					 */
					return Text::plocalize("{{number}} quadrillion", "{{number}} quadrillion", $n, 'number', self::class, $text_options);
			}
			return $n . ($multiple_row['label'] === '' ? '' : " {$multiple_row['label']}");
		}
		return $n . ($multiple_row['symbol'] === '' ? '' : $multiple_row['symbol']);
	}
	
	/**
	 * Retrieve machine-readable number from a given human one.
	 * 
	 * The returning number represents the given one in a machine-readable format, 
	 * by converting it as shown in the examples below:<br>
	 * &nbsp; &#8226; &nbsp; <samp>150</samp> returns <code>150</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>84.29K</samp> or <samp>84.29 thousand</samp> returns <code>84290</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>285M</samp> or <samp>285 million</samp> returns <code>285000000</code>.<br>
	 * &nbsp; &#8226; &nbsp; <samp>5.8B</samp> or <samp>5.8 billion</samp> returns <code>5800000000</code>.<br>
	 * <br>
	 * The short numeric scale is the one used, therefore 1 billion is considered to be 1e9 (1000000000) and not 1e12 (1000000000000).
	 * 
	 * @since 1.0.0
	 * @param string $number <p>The human-readable number to retrieve from.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Math\Exceptions\MnumberInvalidNumber
	 * @return int <p>The machine-readable number from the given human one.</p>
	 */
	final public static function mnumber(string $number) : int
	{
		//parse
		if (!preg_match('/^\s*([\-+])?(\d+([\.,]\d+)?)\s*([^\s]+)?\s*$/', $number, $matches)) {
			throw new Exceptions\MnumberInvalidNumber(['number' => $number]);
		}
		$sign = $matches[1];
		$n = (float)str_replace(',', '.', $matches[2]);
		$multiple = $matches[4] ?? '';
		
		//calculate
		if (!self::evaluateMultiple($multiple)) {
			throw new Exceptions\MnumberInvalidNumber(['number' => $number]);
		}
		$n *= $multiple;
		if ($sign === '-') {
			$n *= -1;
		}
		return (int)$n;
	}
	
	/**
	 * Evaluate a given value as a multiple.
	 * 
	 * Only the following types and formats can be evaluated into a multiple:<br>
	 * &nbsp; &#8226; &nbsp; an integer as a power of 10, such as: <code>1000</code> for thousands;<br>
	 * &nbsp; &#8226; &nbsp; an SI symbol string, such as: <code>"k"</code> for thousands;<br>
	 * &nbsp; &#8226; &nbsp; a name string in English, such as: <code>"thousand"</code> for thousands.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a multiple.</p>
	 */
	final public static function evaluateMultiple(&$value, bool $nullable = false) : bool
	{
		//multiples
		if (empty(self::$multiples)) {
			foreach (self::MULTIPLES_TABLE as $row) {
				foreach (['number', 'symbol', 'label'] as $column) {
					self::$multiples[(string)$row[$column]] = (int)$row['number'];
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
