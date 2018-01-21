<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Utilities\Type\Exceptions;

/**
 * Core type utility class.
 * 
 * This utility implements a set of methods used to check, validate and retrieve information from PHP types, 
 * being mostly focused in scalars, objects and classes.<br>
 * <br>
 * For functions or callables see the <code>\Feralygon\Kit\Core\Utilities\Call</code> class, 
 * while for arrays see the <code>\Feralygon\Kit\Core\Utilities\Data</code> class instead.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Utilities\Call
 * @see \Feralygon\Kit\Core\Utilities\Data
 * @see https://php.net/manual/en/language.types.php
 */
final class Type extends Utility
{
	//Private constants
	/** Phpfy non-associative array maximum pretty output horizontal length. */
	private const PHPFY_NONASSOC_ARRAY_PRETTY_MAX_HORIZONTAL_LENGTH = 50;
	
	
	
	//Final public static methods
	/**
	 * Generate PHP code from a given value.
	 * 
	 * The returning PHP code can be evaluated in order to run as PHP.<br>
	 * <br>
	 * By omission, the return is optimized to be as short as possible, but the <var>$pretty</var> parameter may optionally 
	 * be set to <code>true</code> to get a more human-readable and visually appealing return.<br>
	 * <br>
	 * This function is similar to <code>var_export</code>, but it gives more control on the return, and it is modernized 
	 * (arrays become <code>[...]</code> instead of the old syntax <code>array(...)</code>).
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to generate from.</p>
	 * @param bool $pretty [default = false] <p>Return human-readable and visually appealing PHP code.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\PhpfyUnsupportedValueType
	 * @return string <p>The generated PHP code from the given value.</p>
	 */
	final public static function phpfy($value, bool $pretty = false) : string
	{
		//null
		if (!isset($value)) {
			return 'null';
		}
		
		//boolean
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}
		
		//integer or float
		if (is_int($value) || is_float($value)) {
			return (string)$value;
		}
		
		//string
		if (is_string($value)) {
			$string = preg_replace('/[$"\\\\]/', '\\\\$0', $value);
			$string = $pretty ? implode("\\n\" . \n\"", explode("\n", $string)) : str_replace(["\n", "\t"], ['\\n', '\\t'], $string);
			$string = str_replace(["\v", "\f", "\r", "\e"], ['\\v', '\\f', '\\r', '\\e'], $string);
			$string = preg_replace_callback('/[\x00-\x08\x0e-\x1a\x1c-\x1f\x7f-\xff]/', function (array $matches) : string {return '\\x' . bin2hex($matches[0]);}, $string);
			return '"' . $string . '"';
		}
		
		//object
		if (is_object($value) && !self::isA($value, \Closure::class)) {
			$properties = [];
			foreach ((array)$value as $name => $v) {
				if (preg_match('/\0[*\w\\\\]+\0(\w+)$/', $name, $matches)) {
					$name = $matches[1];
				}
				$properties[$name] = $v;
			}
			return '\\' . get_class($value) . '::__set_state(' . self::phpfy($properties, $pretty) . ')';
		}
		
		//array
		if (is_array($value)) {
			//empty
			if (empty($value)) {
				return '[]';
			}
			
			//process
			$is_assoc = Data::isAssociative($value);
			$array = [];
			foreach ($value as $k => $v) {
				$array[] = ($is_assoc ? (is_int($k) ? $k : self::phpfy((string)$k, $pretty)) . ($pretty ? ' => ' : '=>') : '') . self::phpfy($v, $pretty);
			}
			
			//return
			if ($pretty) {
				if (!$is_assoc) {
					$string = '[' . implode(', ', $array) . ']';
					if (strlen($string) <= self::PHPFY_NONASSOC_ARRAY_PRETTY_MAX_HORIZONTAL_LENGTH) {
						return $string;
					}
				}
				return "[\n" . Text::indentate(implode(",\n", $array)) . "\n]";
			}
			return '[' . implode(',', $array) . ']';
		}
		
		//callable
		if (is_callable($value)) {
			return Call::source($value, Call::SOURCE_CONSTANTS_VALUES | Call::SOURCE_NO_MIXED_TYPE | Call::SOURCE_CLASSES_LEADING_SLASH);
		}
		
		//exception
		throw new Exceptions\PhpfyUnsupportedValueType(['value' => $value, 'type' => gettype($value)]);
	}
	
	/**
	 * Evaluate a given value as a boolean.
	 * 
	 * Only the following types and formats can be evaluated into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean, as: <code>false</code> for boolean <code>false</code>, and <code>true</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a boolean.</p>
	 */
	final public static function evaluateBoolean(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceBoolean($value, $nullable);
		} catch (Exceptions\BooleanCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a boolean.
	 * 
	 * Only the following types and formats can be coerced into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean, as: <code>false</code> for boolean <code>false</code>, and <code>true</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\BooleanCoercionFailed
	 * @return bool|null <p>The given value coerced into a boolean.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceBoolean($value, bool $nullable = false) : ?bool
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\BooleanCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\BooleanCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (is_bool($value)) {
			return $value;
		} elseif (is_int($value) && ($value === 0 || $value === 1)) {
			return $value === 1;
		} elseif (is_float($value) && ($value === 0.0 || $value === 1.0)) {
			return $value === 1.0;
		} elseif (is_string($value)) {
			$value = strtolower($value);
			if (in_array($value, ['0', '1', 'f', 't', 'false', 'true', 'off', 'on', 'no', 'yes'], true)) {
				return in_array($value, ['1', 't', 'true', 'on', 'yes'], true);
			}
		}
		throw new Exceptions\BooleanCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\BooleanCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a boolean:\n" . 
				" - a boolean, as: false for boolean false, and true for boolean true;\n" . 
				" - an integer, as: 0 for boolean false, and 1 for boolean true;\n" . 
				" - a float, as: 0.0 for boolean false, and 1.0 for boolean true;\n" . 
				" - a string, as: \"0\", \"f\", \"false\", \"off\" or \"no\" for boolean false, and \"1\", \"t\", \"true\", \"on\" or \"yes\" for boolean true."
		]);
	}
	
	/**
	 * Evaluate a given value as a number.
	 * 
	 * Only the following types and formats can be evaluated into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a number.</p>
	 */
	final public static function evaluateNumber(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceNumber($value, $nullable);
		} catch (Exceptions\NumberCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a number.
	 * 
	 * Only the following types and formats can be coerced into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\NumberCoercionFailed
	 * @return int|float|null <p>The given value coerced into a number.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceNumber($value, bool $nullable = false)
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\NumberCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\NumberCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (is_int($value)) {
			return $value;
		} elseif (is_float($value)) {
			return $value === floor($value) ? (int)$value : $value;
		} elseif (is_string($value)) {
			//numeric
			$v = str_replace(',', '.', $value);
			if (is_numeric($v) || preg_match('/^0x[\da-f]{1,16}$/i', $v)) {
				$value = strpos($v, '.') !== false || preg_match('/^[\-+]?\d+e[\-+]?\d+$/i', $v) ? (float)$v : intval($v, 0);
				if (is_float($value) && $value === floor($value)) {
					$value = (int)$value;
				}
				return $value;
			}
				
			//human-readable
			try {
				return Math::mnumber($value);
			} catch (Math\Exceptions\Mnumber $exception) {}
				
			//human-readable (bytes)
			try {
				return Byte::mvalue($value);
			} catch (Byte\Exceptions\Mvalue $exception) {}
		}
		throw new Exceptions\NumberCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\NumberCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a number:\n" . 
				" - an integer, such as: 123000 for 123000;\n" . 
				" - a float, such as: 123000.45 for 123000.45;\n" . 
				" - a numeric string, such as: \"123000.45\" or \"123000,45\" for 123000.45;\n" . 
				" - a numeric string in exponential notation, such as: \"123e3\" or \"123E3\" for 123000;\n" . 
				" - a numeric string in octal notation, such as: \"0360170\" for 123000;\n" . 
				" - a numeric string in hexadecimal notation, such as: \"0x1e078\" or \"0x1E078\" for 123000;\n" . 
				" - a human-readable numeric string, such as: \"123k\" or \"123 thousand\" for 123000;\n" . 
				" - a human-readable numeric string in bytes, such as: \"123kB\" or \"123 kilobytes\" for 123000."
		]);
	}
	
	/**
	 * Evaluate a given value as an integer.
	 * 
	 * Only the following types and formats can be evaluated into an integer:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an integer.</p>
	 */
	final public static function evaluateInteger(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceInteger($value, $nullable);
		} catch (Exceptions\IntegerCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an integer.
	 * 
	 * Only the following types and formats can be coerced into an integer:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a whole float, such as: <code>123000.0</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123kB"</code> or <code>"123 kilobytes"</code> for <code>123000</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\IntegerCoercionFailed
	 * @return int|null <p>The given value coerced into an integer.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceInteger($value, bool $nullable = false) : ?int
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\IntegerCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (self::evaluateNumber($value) && is_int($value)) {
			return $value;
		}
		throw new Exceptions\IntegerCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into an integer:\n" . 
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
	 * Evaluate a given value as a float.
	 * 
	 * Only the following types and formats can be evaluated into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123.45kB"</code> or <code>"123.45 kilobytes"</code> for <code>123450.0</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a float.</p>
	 */
	final public static function evaluateFloat(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceFloat($value, $nullable);
		} catch (Exceptions\FloatCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a float.
	 * 
	 * Only the following types and formats can be coerced into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string in bytes, such as: <code>"123.45kB"</code> or <code>"123.45 kilobytes"</code> for <code>123450.0</code>.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\FloatCoercionFailed
	 * @return float|null <p>The given value coerced into a float.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceFloat($value, bool $nullable = false) : ?float
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\FloatCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\FloatCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (self::evaluateNumber($value)) {
			return (float)$value;
		}
		throw new Exceptions\FloatCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\FloatCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a float:\n" . 
				" - an integer, such as: 123000 for 123000.0;\n" . 
				" - a float, such as: 123000.45 for 123000.45;\n" . 
				" - a numeric string, such as: \"123000.45\" or \"123000,45\" for 123000.45;\n" . 
				" - a numeric string in exponential notation, such as: \"123e3\" or \"123E3\" for 123000.0;\n" . 
				" - a numeric string in octal notation, such as: \"0360170\" for 123000.0;\n" . 
				" - a numeric string in hexadecimal notation, such as: \"0x1e078\" or \"0x1E078\" for 123000.0;\n" . 
				" - a human-readable numeric string, such as: \"123.45k\" or \"123.45 thousand\" for 123450.0;\n" . 
				" - a human-readable numeric string in bytes, such as: \"123.45kB\" or \"123.45 kilobytes\" for 123450.0."
		]);
	}
	
	/**
	 * Evaluate a given value as a string.
	 * 
	 * Only a string, integer or float can be evaluated into a string.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a string.</p>
	 */
	final public static function evaluateString(&$value, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceString($value, $nullable);
		} catch (Exceptions\StringCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a string.
	 * 
	 * Only a string, integer or float can be coerced into a string.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\StringCoercionFailed
	 * @return string|null <p>The given value coerced into a string.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceString($value, bool $nullable = false) : ?string
	{
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\StringCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\StringCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (is_string($value)) {
			return $value;
		} elseif (is_int($value) || is_float($value)) {
			return (string)$value;
		}
		throw new Exceptions\StringCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\StringCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only a string, integer or float can be coerced into a string."
		]);
	}
	
	/**
	 * Evaluate a given value as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a class.</p>
	 */
	final public static function evaluateClass(&$value, $base_object_class = null, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceClass($value, $base_object_class, $nullable);
		} catch (Exceptions\ClassCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into a class.
	 * 
	 * Only a class string or object can be coerced into a class.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\ClassCoercionFailed
	 * @return string|null <p>The given value coerced into a class.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceClass($value, $base_object_class = null, bool $nullable = false) : ?string
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\ClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$base_class = isset($base_object_class) ? self::class($base_object_class) : null;
		try {
			$class = self::class($value);
			if (isset($base_class) && !self::isA($class, $base_class)) {
				throw new Exceptions\ClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only a class which is or extends from {{base_class}} is allowed.",
						['base_class' => Text::stringify($base_class)]
					)
				]);
			}
			$value = $class;
		} catch (Exceptions\InvalidObjectClass | Exceptions\ClassNotFound $exception) {
			throw new Exceptions\ClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_INVALID,
				'error_message' => "Only a class string or object can be coerced into a class."
			]);
		}
		return $value;
	}
	
	/**
	 * Evaluate a given value as an object.
	 * 
	 * Only a class string or object can be evaluated into an object.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param array $arguments [default = []] <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an object.</p>
	 */
	final public static function evaluateObject(&$value, $base_object_class = null, array $arguments = [], bool $nullable = false) : bool
	{
		try {
			$value = self::coerceObject($value, $base_object_class, $arguments, $nullable);
		} catch (Exceptions\ObjectCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an object.
	 * 
	 * Only a class string or object can be coerced into an object.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param array $arguments [default = []] <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\ObjectCoercionFailed
	 * @return object|null <p>The given value coerced into an object.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceObject($value, $base_object_class = null, array $arguments = [], bool $nullable = false)
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\ObjectCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$base_class = isset($base_object_class) ? self::class($base_object_class) : null;
		try {
			$class = self::class($value);
			if (isset($base_class) && !self::isA($class, $base_class)) {
				throw new Exceptions\ObjectCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only an object which is or extends from {{base_class}} is allowed.",
						['base_class' => Text::stringify($base_class)]
					)
				]);
			} elseif (!is_object($value)) {
				try {
					$value = self::construct($class, $arguments);
				} catch (\Exception $exception) {
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}}, with the following message: {{message}}", [
								'class' => Text::stringify($class),
								'exception' => Text::stringify($exception),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		} catch (Exceptions\InvalidObjectClass | Exceptions\ClassNotFound $exception) {
			throw new Exceptions\ObjectCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INVALID,
				'error_message' => "Only a class string or object can be coerced into an object."
			]);
		}
		return $value;
	}
	
	/**
	 * Evaluate a given value as an object or class.
	 * 
	 * Only a class string or object can be coerced into an object or class.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into an object or class.</p>
	 */
	final public static function evaluateObjectClass(&$value, $base_object_class = null, bool $nullable = false) : bool
	{
		try {
			$value = self::coerceObjectClass($value, $base_object_class, $nullable);
		} catch (Exceptions\ObjectClassCoercionFailed $exception) {
			return false;
		}
		return true;
	}
	
	/**
	 * Coerce a given value into an object or class.
	 * 
	 * Only a class string or object can be coerced into an object or class.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $base_object_class [default = null] <p>The base object or class which the given value must be or extend from.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\ObjectClassCoercionFailed
	 * @return object|string|null <p>The given value coerced into an object or class.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerceObjectClass($value, $base_object_class = null, bool $nullable = false)
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\ObjectClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$base_class = isset($base_object_class) ? self::class($base_object_class) : null;
		try {
			$class = self::class($value);
			if (isset($base_class) && !self::isA($class, $base_class)) {
				throw new Exceptions\ObjectClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only a class or object which is or extends from {{base_class}} is allowed.",
						['base_class' => Text::stringify($base_class)]
					)
				]);
			}
		} catch (Exceptions\InvalidObjectClass | Exceptions\ClassNotFound $exception) {
			throw new Exceptions\ObjectClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INVALID,
				'error_message' => "Only a class string or object can be coerced into an object or class."
			]);
		}
		return $value;
	}
	
	/**
	 * Construct a new instance from a given object or class reference.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class reference to construct from.</p>
	 * @param array $arguments [default = []] <p>The class constructor arguments to instantiate with.</p>
	 * @return object <p>The new instance from the given object or class reference.</p>
	 */
	final public static function construct($object_class, array $arguments = [])
	{
		return (new \ReflectionClass(self::class($object_class)))->newInstanceArgs($arguments);
	}
	
	/**
	 * Check if a given object or class extends from or is of the same class as a given base object or class.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @param object|string $base_object_class <p>The base object or class to check against.</p>
	 * @return bool <p>Boolean <code>true</code> if the given object or class extends from or 
	 * is of the same class as the given base object or class.</p>
	 */
	final public static function isA($object_class, $base_object_class) : bool
	{
		return is_a(self::class($object_class), self::class($base_object_class), true);
	}
	
	/**
	 * Check if all given objects or classes extend from or are of the same class as a given base object or class.
	 * 
	 * @since 1.0.0
	 * @param object[]|string[] $objects_classes <p>The objects or classes to check.</p>
	 * @param object|string $base_object_class <p>The base object or class to check against.</p>
	 * @return bool <p>Boolean <code>true</code> if all the given objects or classes extend from or
	 * are of the same class as the given base object or class.</p>
	 */
	final public static function areA(array $objects_classes, $base_object_class) : bool
	{
		$base_class = self::class($base_object_class);
		foreach ($objects_classes as $object_class) {
			if (!is_a(self::class($object_class), $base_class, true)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if a given object or class extends from or is of the same class as any given base objects or classes.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @param object[]|string[] $base_objects_classes <p>The base objects or classes to check against.</p>
	 * @return bool <p>Boolean <code>true</code> if the given object or class extends from or
	 * is of the same class as any of the given base objects or classes.</p>
	 */
	final public static function isAny($object_class, array $base_objects_classes) : bool
	{
		$class = self::class($object_class);
		foreach ($base_objects_classes as $base_object_class) {
			if (is_a($class, self::class($base_object_class), true)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check if all given objects or classes extend from or are of the same class as any given base objects or classes.
	 * 
	 * @since 1.0.0
	 * @param object[]|string[] $objects_classes <p>The objects or classes to check.</p>
	 * @param object[]|string[] $base_objects_classes <p>The base objects or classes to check against.</p>
	 * @return bool <p>Boolean <code>true</code> if the given objects or classes extend from or
	 * are of the same class as any of the given base objects or classes.</p>
	 */
	final public static function areAny(array $objects_classes, array $base_objects_classes) : bool
	{
		//classes
		$classes = [];
		foreach ($objects_classes as $object_class) {
			$classes[] = self::class($object_class);
		}
		
		//base classes
		$base_classes = [];
		foreach ($base_objects_classes as $base_object_class) {
			$base_classes[] = self::class($base_object_class);
		}
		
		//check
		foreach ($classes as $class) {
			$is_any = false;
			foreach ($base_classes as $base_class) {
				if (is_a($class, $base_class, true)) {
					$is_any = true;
					break;
				}
			}
			if (!$is_any) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if a given object or class implements a given interface.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @param string $interface <p>The interface to check against.</p>
	 * @return bool <p>Boolean <code>true</code> if the given object or class implements the given interface.</p>
	 */
	final public static function implements($object_class, string $interface) : bool
	{
		return self::implementsAny($object_class, [$interface]);
	}
	
	/**
	 * Check if a given object or class implements any given interfaces.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @param string[] $interfaces <p>The interfaces to check against.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\InvalidInterface
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\InterfaceNotFound
	 * @return bool <p>Boolean <code>true</code> if the given object or class implements any of the given interfaces.</p>
	 */
	final public static function implementsAny($object_class, array $interfaces) : bool
	{
		foreach ($interfaces as $interface) {
			if (!is_string($interface)) {
				throw new Exceptions\InvalidInterface(['interface' => $interface]);
			} elseif (!interface_exists($interface)) {
				throw new Exceptions\InterfaceNotFound(['interface' => $interface]);
			}
		}
		return !empty(array_intersect_key(class_implements(self::class($object_class)), array_flip($interfaces)));
	}
	
	/**
	 * Check if a given object or class implements all given interfaces.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @param string[] $interfaces <p>The interfaces to check against.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\InvalidInterface
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\InterfaceNotFound
	 * @return bool <p>Boolean <code>true</code> if the given object or class implements all of the given interfaces.</p>
	 */
	final public static function implementsAll($object_class, array $interfaces) : bool
	{
		foreach ($interfaces as $interface) {
			if (!is_string($interface)) {
				throw new Exceptions\InvalidInterface(['interface' => $interface]);
			} elseif (!interface_exists($interface)) {
				throw new Exceptions\InterfaceNotFound(['interface' => $interface]);
			}
		}
		return count(array_intersect_key(class_implements(self::class($object_class)), array_flip($interfaces))) === count($interfaces);
	}
	
	/**
	 * Check if a given object or class is anonymous.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to check.</p>
	 * @return bool <p>Boolean <code>true</code> if the given object or class is anonymous.</p>
	 */
	final public static function isAnonymous($object_class) : bool
	{
		return (bool)preg_match('/^class@anonymous/', self::class($object_class));
	}
	
	/**
	 * Retrieve class from a given object or class.
	 * 
	 * The leading backslash character <samp>\</samp> is never prepended to the returned class.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\InvalidObjectClass
	 * @throws \Feralygon\Kit\Core\Utilities\Type\Exceptions\ClassNotFound
	 * @return string <p>The class from the given object or class.</p>
	 */
	final public static function class($object_class) : string
	{
		if (is_object($object_class)) {
			return get_class($object_class);
		} elseif (!is_string($object_class)) {
			throw new Exceptions\InvalidObjectClass(['object_class' => $object_class]);
		} elseif (!class_exists($object_class)) {
			throw new Exceptions\ClassNotFound(['class' => $object_class]);
		}
		return ltrim($object_class, '\\');
	}
	
	/**
	 * Retrieve basename from a given object or class.
	 * 
	 * The returning basename is the class name without its namespace (class short name).
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @return string <p>The basename from the given object or class.</p>
	 */
	final public static function basename($object_class) : string
	{
		return (new \ReflectionClass(self::class($object_class)))->getShortName();
	}
	
	/**
	 * Retrieve namespace from a given object or class.
	 * 
	 * The returning namespace does not have the leading backslash character <samp>\</samp>, 
	 * thus an empty namespace is returned for the global one.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/language.namespaces.php
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @param int|null $depth [default = null] <p>The depth limit to retrieve with.<br>
	 * If set to a number less than <code>0</code>, the limit is applied backwards (starting at the end of the namespace).<br>
	 * If not set, no limit is applied.</p>
	 * @return string <p>The namespace from the given object or class.</p>
	 */
	final public static function namespace($object_class, ?int $depth = null) : string
	{
		$namespace = (new \ReflectionClass(self::class($object_class)))->getNamespaceName();
		if (isset($depth)) {
			$nameparts = explode('\\', $namespace);
			$namespace = implode('\\', $depth >= 0 ? array_slice($nameparts, 0, $depth) : array_slice($nameparts, $depth));
		}
		return $namespace;
	}
	
	/**
	 * Retrieve filepath from a given object or class.
	 * 
	 * The returning filepath is the absolute file path in the filesystem where the class is declared.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @return string <p>The filepath from the given object or class or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function filepath($object_class) : ?string
	{
		$filepath = (new \ReflectionClass(self::class($object_class)))->getFileName();
		return $filepath === false ? null : $filepath;
	}
	
	/**
	 * Retrieve directory from a given object or class.
	 * 
	 * The returning directory is the absolute directory path in the filesystem where the class is declared.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @return string <p>The directory from the given object or class or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function directory($object_class) : ?string
	{
		$filepath = self::filepath($object_class);
		return isset($filepath) ? dirname($filepath) : null;
	}
	
	/**
	 * Retrieve filename from a given object or class.
	 * 
	 * The returning filename is the complete name of the file where the class is declared.
	 * 
	 * @since 1.0.0
	 * @param object|string $object_class <p>The object or class to retrieve from.</p>
	 * @return string <p>The filename from the given object or class or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function filename($object_class) : ?string
	{
		$filepath = self::filepath($object_class);
		return isset($filepath) ? basename($filepath) : null;
	}
}
