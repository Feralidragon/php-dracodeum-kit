<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Type\{
	Options,
	Exceptions
};
use Dracodeum\Kit\Interfaces\{
	Integerable as IIntegerable,
	Floatable as IFloatable,
	Stringifiable as IStringifiable,
	IntegerInstantiable as IIntegerInstantiable,
	FloatInstantiable as IFloatInstantiable,
	StringInstantiable as IStringInstantiable,
	ArrayInstantiable as IArrayInstantiable,
	CallableInstantiable as ICallableInstantiable,
	Uninstantiable as IUninstantiable,
	Cloneable as ICloneable,
	Uncloneable as IUncloneable,
	Readonlyable as IReadonlyable,
	Keyable as IKeyable,
	Persistable as IPersistable,
	Unpersistable as IUnpersistable
};

/**
 * This utility implements a set of methods used to check, validate and get information from PHP types, 
 * being mostly focused in scalars, objects and classes.
 * 
 * For functions or callables see the <code>Dracodeum\Kit\Utilities\Call</code> class, 
 * while for arrays see the <code>Dracodeum\Kit\Utilities\Data</code> class instead.
 * 
 * @see \Dracodeum\Kit\Utilities\Call
 * @see \Dracodeum\Kit\Utilities\Data
 * @see https://php.net/manual/en/language.types.php
 */
final class Type extends Utility
{
	//Public constants
	/** Boolean <code>false</code> strings. */
	public const BOOLEAN_FALSE_STRINGS = ['0', 'f', 'false', 'off', 'no'];
	
	/** Boolean <code>true</code> strings. */
	public const BOOLEAN_TRUE_STRINGS = ['1', 't', 'true', 'on', 'yes'];
	
	/** Integer maximum supported number of bits (signed). */
	public const INTEGER_BITS_MAX_SIGNED = 64;
	
	/** Integer maximum supported number of bits (unsigned). */
	public const INTEGER_BITS_MAX_UNSIGNED = 63;
	
	/** All supported integer bits fully on (unsigned). */
	public const INTEGER_BITS_FULL_UNSIGNED = 0x7fffffffffffffff;
	
	
	
	//Private constants
	/** Phpfy non-associative array maximum pretty output horizontal length. */
	private const PHPFY_NONASSOC_ARRAY_PRETTY_MAX_HORIZONTAL_LENGTH = 50;
	
	
	
	//Final public static methods
	/**
	 * Generate PHP code from a given value.
	 * 
	 * The returning PHP code can be evaluated in order to run as PHP.<br>
	 * <br>
	 * By omission, the returning code is optimized to be as short as possible, but <var>$options->pretty</var> may be 
	 * optionally set to boolean <code>true</code> to get a more human-readable and visually appealing PHP code.<br>
	 * <br>
	 * This function is similar to <code>var_export</code>, but it provides more control on the returning code style, 
	 * and it is modernized (arrays become <code>[...]</code> instead of the old syntax <code>array(...)</code>).<br>
	 * <br>
	 * For objects, only those implementing the <code>__set_state</code> method, 
	 * or both <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * and <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interfaces, are supported.
	 * 
	 * @see https://php.net/manual/en/function.var-export.php
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.set-state
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @param mixed $value
	 * <p>The value to generate from.</p>
	 * @param \Dracodeum\Kit\Utilities\Type\Options\Phpfy|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\Phpfy\UnsupportedValueType
	 * @return string|null
	 * <p>The generated PHP code from the given value.<br>
	 * If <var>$options->no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be generated.</p>
	 */
	final public static function phpfy($value, $options = null): ?string
	{
		//initialize
		$options = Options\Phpfy::coerce($options);
		
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
			$string = $options->pretty
				? implode("\\n\" . \n\"", explode("\n", $string))
				: str_replace(["\n", "\t"], ['\\n', '\\t'], $string);
			$string = str_replace(["\v", "\f", "\r", "\e"], ['\\v', '\\f', '\\r', '\\e'], $string);
			$string = preg_replace_callback(
				'/[\x00-\x08\x0e-\x1a\x1c-\x1f\x7f-\xff]/',
				function (array $matches): string {
					return '\\x' . bin2hex($matches[0]);
				},
				$string
			);
			return '"' . $string . '"';
		}
		
		//object
		if (is_object($value) && !self::isA($value, \Closure::class)) {
			$class = get_class($value);
			if (method_exists($value, '__set_state')) {
				$properties = [];
				foreach ((array)$value as $name => $v) {
					if (preg_match('/\0[*\w\\\\]+\0(?P<name>\w+)$/', $name, $matches)) {
						$name = $matches['name'];
					}
					$properties[$name] = $v;
				}
				$php = self::phpfy($properties, $options);
				return isset($php) ? "\\{$class}::__set_state({$php})" : null;
			} elseif ($value instanceof IArrayInstantiable && Data::evaluate($value)) {
				$php = self::phpfy($value, $options);
				return isset($php) ? "\\{$class}::fromArray({$php})" : null;
			} elseif ($options->no_throw) {
				return null;
			}
			throw new Exceptions\Phpfy\UnsupportedValueType([
				'value' => $value,
				'hint_message' => "Only an object implementing the \"__set_state\" method, " . 
					"or both \"Dracodeum\\Kit\\Interfaces\\Arrayable\" " . 
					"and \"Dracodeum\\Kit\\Interfaces\\ArrayInstantiable\" interfaces, is supported."
			]);
		}
		
		//array
		if (is_array($value)) {
			//empty
			if (empty($value)) {
				return '[]';
			}
			
			//process
			$is_assoc = Data::associative($value);
			$array = [];
			foreach ($value as $k => $v) {
				//key
				$k_php = '';
				if ($is_assoc) {
					if (is_int($k)) {
						$k_php = (string)$k;
					} else {
						$k_php = self::phpfy($k, $options);
						if (!isset($k_php)) {
							return null;
						}
					}
					$k_php .= $options->pretty ? ' => ' : '=>';
				}
				
				//value
				$v_php = self::phpfy($v, $options);
				if (!isset($v_php)) {
					return null;
				}
				
				//array
				$array[] = $k_php . $v_php;
			}
			
			//return
			if ($options->pretty) {
				if (!$is_assoc) {
					$string = '[' . implode(', ', $array) . ']';
					if (strlen($string) <= self::PHPFY_NONASSOC_ARRAY_PRETTY_MAX_HORIZONTAL_LENGTH) {
						return $string;
					}
				}
				$spaces = $options->spaces;
				$array_php = Text::indentate(implode(",\n", $array), $spaces ?? 1, isset($spaces) ? ' ' : "\t");
				return "[\n{$array_php}\n]";
			}
			return '[' . implode(',', $array) . ']';
		}
		
		//callable
		if (is_callable($value)) {
			return Call::source(
				$value,
				Call::SOURCE_CONSTANTS_VALUES | Call::SOURCE_NO_MIXED_TYPE | Call::SOURCE_NAMESPACES_LEADING_SLASH
			);
		}
		
		//finalize
		if ($options->no_throw) {
			return null;
		}
		throw new Exceptions\Phpfy\UnsupportedValueType([$value]);
	}
	
	/**
	 * Evaluate a given value as a boolean.
	 * 
	 * Only the following types and formats can be evaluated into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a boolean.</p>
	 */
	final public static function evaluateBoolean(&$value, bool $nullable = false): bool
	{
		return self::processBooleanCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a boolean.
	 * 
	 * Only the following types and formats can be coerced into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\BooleanCoercionFailed
	 * @return bool|null
	 * <p>The given value coerced into a boolean.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceBoolean($value, bool $nullable = false): ?bool
	{
		self::processBooleanCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a boolean.
	 * 
	 * Only the following types and formats can be coerced into a boolean:<br>
	 * &nbsp; &#8226; &nbsp; a boolean;<br>
	 * &nbsp; &#8226; &nbsp; an integer, as: <code>0</code> for boolean <code>false</code>, 
	 * and <code>1</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, as: <code>0.0</code> for boolean <code>false</code>, 
	 * and <code>1.0</code> for boolean <code>true</code>;<br>
	 * &nbsp; &#8226; &nbsp; a string, as: <code>"0"</code>, <code>"f"</code>, <code>"false"</code>, 
	 * <code>"off"</code> or <code>"no"</code> for boolean <code>false</code>, 
	 * and <code>"1"</code>, <code>"t"</code>, <code>"true"</code>, 
	 * <code>"on"</code> or <code>"yes"</code> for boolean <code>true</code>.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\BooleanCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a boolean.</p>
	 */
	final public static function processBooleanCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\BooleanCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\BooleanCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		if (is_bool($value)) {
			return true;
		} elseif (is_int($value) && ($value === 0 || $value === 1)) {
			$value = $value === 1;
			return true;
		} elseif (is_float($value) && ($value === 0.0 || $value === 1.0)) {
			$value = $value === 1.0;
			return true;
		} elseif (is_string($value)) {
			$boolean = strtolower($value);
			if (in_array($boolean, self::BOOLEAN_FALSE_STRINGS, true)) {
				$value = false;
				return true;
			} elseif (in_array($boolean, self::BOOLEAN_TRUE_STRINGS, true)) {
				$value = true;
				return true;
			}
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		$false_list_string = Text::commify(self::BOOLEAN_FALSE_STRINGS, null, 'or', true);
		$true_list_string = Text::commify(self::BOOLEAN_TRUE_STRINGS, null, 'or', true);
		throw new Exceptions\BooleanCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\BooleanCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a boolean:\n" . 
				" - a boolean, as: false for boolean false, and true for boolean true;\n" . 
				" - an integer, as: 0 for boolean false, and 1 for boolean true;\n" . 
				" - a float, as: 0.0 for boolean false, and 1.0 for boolean true;\n" . 
				" - a string, as: {$false_list_string} for boolean false, and {$true_list_string} for boolean true."
		]);
	}
	
	/**
	 * Evaluate a given value as a number.
	 * 
	 * Only the following types and formats can be evaluated into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a number.</p>
	 */
	final public static function evaluateNumber(&$value, bool $nullable = false): bool
	{
		return self::processNumberCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a number.
	 * 
	 * Only the following types and formats can be coerced into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\NumberCoercionFailed
	 * @return int|float|null
	 * <p>The given value coerced into a number.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceNumber($value, bool $nullable = false)
	{
		self::processNumberCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a number.
	 * 
	 * Only the following types and formats can be coerced into a number:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123k"</code> or <code>"123 thousand"</code> for <code>123000</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\NumberCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a number.</p>
	 */
	final public static function processNumberCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\NumberCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\NumberCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		if (is_int($value)) {
			return true;
		} elseif (is_float($value)) {
			if ($value === floor($value)) {
				$value = (int)$value;
			}
			return true;
		} elseif (is_string($value)) {
			//numeric
			$number = str_replace(',', '.', $value);
			if (is_numeric($number) || preg_match('/^0x[\da-f]{1,16}$/i', $number)) {
				if (strpos($number, '.') !== false || preg_match('/^[\-+]?\d+e[\-+]?\d+$/i', $number)) {
					$number = (float)$number;
					if ($number === floor($number)) {
						$number = (int)$number;
					}
				} else {
					$number = intval($number, 0);
				}
				$value = $number;
				return true;
			}
			
			//human-readable
			$number = Math::mnumber($value, true);
			if (isset($number)) {
				$value = $number;
				return true;
			}
		} elseif (is_object($value) && $value instanceof IIntegerable) {
			$value = $value->toInteger();
			return true;
		} elseif (is_object($value) && $value instanceof IFloatable) {
			$value = $value->toFloat();
			if ($value === floor($value)) {
				$value = (int)$value;
			}
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
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
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Integerable\" interface;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Floatable\" interface."
		]);
	}
	
	/**
	 * Evaluate a given value as an integer.
	 * 
	 * Only the following types and formats can be evaluated into an integer:<br>
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $unsigned [default = false]
	 * <p>Evaluate as an unsigned integer.</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to evaluate with.<br>
	 * <br>
	 * For signed integers, the maximum allowed number is <code>64</code>, 
	 * while for unsigned integers this number is <code>63</code>.<br>
	 * If not set, then the number of bits to evaluate with becomes system dependent.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an integer.</p>
	 */
	final public static function evaluateInteger(
		&$value, bool $unsigned = false, ?int $bits = null, bool $nullable = false
	): bool
	{
		return self::processIntegerCoercion($value, $unsigned, $bits, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an integer.
	 * 
	 * Only the following types and formats can be coerced into an integer:<br>
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $unsigned [default = false]
	 * <p>Coerce as an unsigned integer.</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to coerce with.<br>
	 * <br>
	 * For signed integers, the maximum allowed number is <code>64</code>, 
	 * while for unsigned integers this number is <code>63</code>.<br>
	 * If not set, then the number of bits to coerce with becomes system dependent.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\IntegerCoercionFailed
	 * @return int|null
	 * <p>The given value coerced into an integer.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceInteger(
		$value, bool $unsigned = false, ?int $bits = null, bool $nullable = false
	): ?int
	{
		self::processIntegerCoercion($value, $unsigned, $bits, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an integer.
	 * 
	 * Only the following types and formats can be coerced into an integer:<br>
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $unsigned [default = false]
	 * <p>Coerce as an unsigned integer.</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to coerce with.<br>
	 * <br>
	 * For signed integers, the maximum allowed number is <code>64</code>, 
	 * while for unsigned integers this number is <code>63</code>.<br>
	 * If not set, then the number of bits to coerce with becomes system dependent.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\IntegerCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an integer.</p>
	 */
	final public static function processIntegerCoercion(
		&$value, bool $unsigned = false, ?int $bits = null, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//guard
		Call::guardParameter(
			'bits', $bits,
			!isset($bits) || ($bits > 0 && (
				(!$unsigned && $bits <= self::INTEGER_BITS_MAX_SIGNED) || 
				($unsigned && $bits <= self::INTEGER_BITS_MAX_UNSIGNED)
			)), [
				'error_message' => "The given number of bits must be greater than {{minimum}} " . 
					"and less than or equal to {{maximum}}.",
				'parameters' => [
					'minimum' => 0,
					'maximum' => $unsigned ? self::INTEGER_BITS_MAX_UNSIGNED : self::INTEGER_BITS_MAX_SIGNED
				]
			]
		);
		
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\IntegerCoercionFailed([
				'value' => $value,
				'unsigned' => $unsigned,
				'bits' => $bits,
				'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$integer = $value;
		if (self::evaluateNumber($integer) && is_int($integer)) {
			if ($unsigned && $integer < 0) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\IntegerCoercionFailed([
					'value' => $value,
					'unsigned' => $unsigned,
					'bits' => $bits,
					'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_UNSIGNED,
					'error_message' => "Only an unsigned integer is allowed."
				]);
			} elseif (isset($bits)) {
				$maximum = self::INTEGER_BITS_FULL_UNSIGNED 
					>> (($unsigned ? self::INTEGER_BITS_MAX_UNSIGNED : self::INTEGER_BITS_MAX_SIGNED) - $bits);
				$minimum = $unsigned ? 0 : -$maximum - 1;
				if ($integer < $minimum || $integer > $maximum) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\IntegerCoercionFailed([
						'value' => $value,
						'unsigned' => $unsigned,
						'bits' => $bits,
						'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_BITS,
						'error_message' => Text::pfill(
							$unsigned
								? "Only an unsigned integer of {{bits}} bit is allowed."
								: "Only an integer of {{bits}} bit is allowed.",
							$unsigned
								? "Only an unsigned integer of {{bits}} bits is allowed."
								: "Only an integer of {{bits}} bits is allowed.",
							$bits, 'bits'
						)
					]);
				}
			}
			$value = $integer;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\IntegerCoercionFailed([
			'value' => $value,
			'unsigned' => $unsigned,
			'bits' => $bits,
			'error_code' => Exceptions\IntegerCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into an integer:\n" . 
				" - an integer, such as: 123000 for 123000;\n" . 
				" - a whole float, such as: 123000.0 for 123000;\n" . 
				" - a numeric string, such as: \"123000\" for 123000;\n" . 
				" - a numeric string in exponential notation, such as: \"123e3\" or \"123E3\" for 123000;\n" . 
				" - a numeric string in octal notation, such as: \"0360170\" for 123000;\n" . 
				" - a numeric string in hexadecimal notation, such as: \"0x1e078\" or \"0x1E078\" for 123000;\n" . 
				" - a human-readable numeric string, such as: \"123k\" or \"123 thousand\" for 123000;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Integerable\" interface;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Floatable\" interface."
		]);
	}
	
	/**
	 * Evaluate a given value as a float.
	 * 
	 * Only the following types and formats can be evaluated into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a float.</p>
	 */
	final public static function evaluateFloat(&$value, bool $nullable = false): bool
	{
		return self::processFloatCoercion($value, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a float.
	 * 
	 * Only the following types and formats can be coerced into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\FloatCoercionFailed
	 * @return float|null
	 * <p>The given value coerced into a float.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceFloat($value, bool $nullable = false): ?float
	{
		self::processFloatCoercion($value, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a float.
	 * 
	 * Only the following types and formats can be coerced into a float:<br>
	 * &nbsp; &#8226; &nbsp; an integer, such as: <code>123000</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a float, such as: <code>123000.45</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string, 
	 * such as: <code>"123000.45"</code> or <code>"123000,45"</code> for <code>123000.45</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in exponential notation, 
	 * such as: <code>"123e3"</code> or <code>"123E3"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in octal notation, 
	 * such as: <code>"0360170"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a numeric string in hexadecimal notation, 
	 * such as: <code>"0x1e078"</code> or <code>"0x1E078"</code> for <code>123000.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; a human-readable numeric string, 
	 * such as: <code>"123.45k"</code> or <code>"123.45 thousand"</code> for <code>123450.0</code>;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Floatable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Integerable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Floatable
	 * @see \Dracodeum\Kit\Interfaces\Integerable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\FloatCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a float.</p>
	 */
	final public static function processFloatCoercion(&$value, bool $nullable = false, bool $no_throw = false): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\FloatCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\FloatCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		$float = $value;
		if (self::evaluateNumber($float)) {
			$value = (float)$float;
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
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
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Floatable\" interface;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Integerable\" interface."
		]);
	}
	
	/**
	 * Evaluate a given value as a string.
	 * 
	 * Only the following types and formats can be evaluated into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a string.</p>
	 */
	final public static function evaluateString(&$value, bool $non_empty = false, bool $nullable = false): bool
	{
		return self::processStringCoercion($value, $non_empty, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a string.
	 * 
	 * Only the following types and formats can be coerced into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\StringCoercionFailed
	 * @return string|null
	 * <p>The given value coerced into a string.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceString($value, bool $non_empty = false, bool $nullable = false): ?string
	{
		self::processStringCoercion($value, $non_empty, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a string.
	 * 
	 * Only the following types and formats can be coerced into a string:<br>
	 * &nbsp; &#8226; &nbsp; a string, integer or float;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>__toString</code> method;<br>
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringifiable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param bool $non_empty [default = false]
	 * <p>Do not allow an empty string value.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\StringCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a string.</p>
	 */
	final public static function processStringCoercion(
		&$value, bool $non_empty = false, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\StringCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\StringCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//coerce
		if (is_string($value)) {
			if ($non_empty && $value === '') {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\StringCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\StringCoercionFailed::ERROR_CODE_EMPTY,
					'error_message' => "An empty string value is not allowed."
				]);
			}
			return true;
		} elseif (is_int($value) || is_float($value)) {
			$value = (string)$value;
			return true;
		} elseif (is_object($value)) {
			if ($value instanceof IStringifiable) {
				$value = $value->toString();
				return true;
			} elseif (method_exists($value, '__toString')) {
				$value = $value->__toString();
				return true;
			}
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\StringCoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\StringCoercionFailed::ERROR_CODE_INVALID,
			'error_message' => "Only the following types and formats can be coerced into a string:\n" . 
				" - a string, integer or float;\n" . 
				" - an object implementing the \"__toString\" method;\n" . 
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Stringifiable\" interface."
		]);
	}
	
	/**
	 * Evaluate a given value as a class.
	 * 
	 * Only a class string or object can be evaluated into a class.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a class.</p>
	 */
	final public static function evaluateClass(&$value, $object_class_interface = null, bool $nullable = false): bool
	{
		return self::processClassCoercion($value, $object_class_interface, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a class.
	 * 
	 * Only a class string or object can be coerced into a class.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ClassCoercionFailed
	 * @return string|null
	 * <p>The given value coerced into a class.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceClass($value, $object_class_interface = null, bool $nullable = false): ?string
	{
		self::processClassCoercion($value, $object_class_interface, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a class.
	 * 
	 * Only a class string or object can be coerced into a class.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ClassCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a class.</p>
	 */
	final public static function processClassCoercion(
		&$value, $object_class_interface = null, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\ClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//class
		$class = self::class($value, true);
		if (!isset($class)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\ClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_INVALID,
				'error_message' => "Only a class string or object can be coerced into a class."
			]);
		}
		
		//object, class or interface
		if (isset($object_class_interface)) {
			$interface = is_string($object_class_interface) ? self::interface($object_class_interface, true) : null;
			if (isset($interface) && !self::implements($class, $interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only a class which implements {{interface}} is allowed.",
						['interface' => Text::stringify($interface, null, ['quote_strings' => true])]
					)
				]);
			} elseif (!isset($interface) && !self::isA($class, $object_class_interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only a class which is or extends from {{base_class}} is allowed.", [
							'base_class' => Text::stringify(
								self::class($object_class_interface), null, ['quote_strings' => true]
							)
						]
					)
				]);
			}
		}
		
		//finalize
		$value = $class;
		return true;
	}
	
	/**
	 * Evaluate a given value as an object.
	 * 
	 * Only the following types and formats can be evaluated into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param array $arguments [default = []]
	 * <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an object.</p>
	 */
	final public static function evaluateObject(
		&$value, $object_class_interface = null, array $arguments = [], bool $nullable = false
	): bool
	{
		return self::processObjectCoercion($value, $object_class_interface, $arguments, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an object.
	 * 
	 * Only the following types and formats can be coerced into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param array $arguments [default = []]
	 * <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ObjectCoercionFailed
	 * @return object|null
	 * <p>The given value coerced into an object.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceObject(
		$value, $object_class_interface = null, array $arguments = [], bool $nullable = false
	): ?object
	{
		self::processObjectCoercion($value, $object_class_interface, $arguments, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an object.
	 * 
	 * Only the following types and formats can be coerced into an object:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param array $arguments [default = []]
	 * <p>The class constructor arguments to instantiate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ObjectCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an object.</p>
	 */
	final public static function processObjectCoercion(
		&$value, $object_class_interface = null, array $arguments = [], bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\ObjectCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//integer
		if (is_int($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IIntegerInstantiable::class)) {
				try {
					$value = $class::fromInteger($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} " . 
								"from integer, with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//float
		if (is_float($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IFloatInstantiable::class)) {
				try {
					$value = $class::fromFloat($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from float, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//string
		if (is_string($value) && !class_exists($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IStringInstantiable::class)) {
				try {
					$value = $class::fromString($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from string, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//array
		if (is_array($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IArrayInstantiable::class)) {
				try {
					$value = $class::fromArray($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from array, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//callable
		if (is_callable($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, ICallableInstantiable::class)) {
				try {
					$value = $class::fromCallable($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} " . 
								"from callable, with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//object
		$object = $value;
		if (!is_object($object)) {
			//class
			$class = self::class($object, true);
			if (!isset($class)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INVALID,
					'error_message' => "Only the following types and formats can be coerced into an object:\n" . 
						" - a class string or object;\n" . 
						" - an integer with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\IntegerInstantiable\" interface;\n" . 
						" - a float with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\FloatInstantiable\" interface;\n" . 
						" - a string with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\StringInstantiable\" interface;\n" . 
						" - an array with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\ArrayInstantiable\" interface;\n" . 
						" - a callable with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\CallableInstantiable\" interface."
				]);
			}
			
			//instantiate
			try {
				$object = self::instantiate($class, ...$arguments);
			} catch (\Exception $exception) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
					'error_message' => Text::fill(
						"An exception {{exception}} was thrown while instantiating class {{class}}, " . 
							"with the following message: {{message}}", [
							'class' => Text::stringify($class, null, ['quote_strings' => true]),
							'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
							'message' => Text::uncapitalize($exception->getMessage(), true)
						]
					)
				]);
			}
		}
		
		//object, class or interface
		if (isset($object_class_interface)) {
			$interface = is_string($object_class_interface) ? self::interface($object_class_interface, true) : null;
			if (isset($interface) && !self::implements($object, $interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only an object which implements {{interface}} is allowed.",
						['interface' => Text::stringify($interface, null, ['quote_strings' => true])]
					)
				]);
			} elseif (!isset($interface) && !self::isA($object, $object_class_interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only an object which is or extends from {{base_class}} is allowed.", [
							'base_class' => Text::stringify(
								self::class($object_class_interface), null, ['quote_strings' => true]
							)
						]
					)
				]);
			}
		}
		
		//finalize
		$value = $object;
		return true;
	}
	
	/**
	 * Evaluate a given value as an object or class.
	 * 
	 * Only the following types and formats can be evaluated into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into an object or class.</p>
	 */
	final public static function evaluateObjectClass(
		&$value, $object_class_interface = null, bool $nullable = false
	): bool
	{
		return self::processObjectClassCoercion($value, $object_class_interface, $nullable, true);
	}
	
	/**
	 * Coerce a given value into an object or class.
	 * 
	 * Only the following types and formats can be coerced into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ObjectClassCoercionFailed
	 * @return object|string|null
	 * <p>The given value coerced into an object or class.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerceObjectClass($value, $object_class_interface = null, bool $nullable = false)
	{
		self::processObjectClassCoercion($value, $object_class_interface, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into an object or class.
	 * 
	 * Only the following types and formats can be coerced into an object or class:<br>
	 * &nbsp; &#8226; &nbsp; a class string or object;<br>
	 * &nbsp; &#8226; &nbsp; an integer with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\IntegerInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a float with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\FloatInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a string with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; an array with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\ArrayInstantiable</code> interface;<br>
	 * &nbsp; &#8226; &nbsp; a callable with an <var>$object_class_interface</var> implementing 
	 * the <code>Dracodeum\Kit\Interfaces\CallableInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\IntegerInstantiable
	 * @see \Dracodeum\Kit\Interfaces\FloatInstantiable
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @see \Dracodeum\Kit\Interfaces\ArrayInstantiable
	 * @see \Dracodeum\Kit\Interfaces\CallableInstantiable
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param object|string|null $object_class_interface [default = null]
	 * <p>The object or class which the given value must be or extend from 
	 * or the interface which the given value must implement.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ObjectClassCoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into an object or class.</p>
	 */
	final public static function processObjectClassCoercion(
		&$value, $object_class_interface = null, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//nullable
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\ObjectClassCoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		}
		
		//integer
		if (is_int($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IIntegerInstantiable::class)) {
				try {
					$value = $class::fromInteger($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectClassCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} " . 
								"from integer, with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//float
		if (is_float($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IFloatInstantiable::class)) {
				try {
					$value = $class::fromFloat($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectClassCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from float, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//string
		if (is_string($value) && !class_exists($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IStringInstantiable::class)) {
				try {
					$value = $class::fromString($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectClassCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from string, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//array
		if (is_array($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, IArrayInstantiable::class)) {
				try {
					$value = $class::fromArray($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectClassCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} from array, " . 
								"with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//callable
		if (is_callable($value) && isset($object_class_interface)) {
			$class = self::class($object_class_interface, true);
			if (isset($class) && self::implements($class, ICallableInstantiable::class)) {
				try {
					$value = $class::fromCallable($value);
					return true;
				} catch (\Exception $exception) {
					if ($no_throw) {
						return false;
					}
					throw new Exceptions\ObjectClassCoercionFailed([
						'value' => $value,
						'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INSTANCE_EXCEPTION,
						'error_message' => Text::fill(
							"An exception {{exception}} was thrown while instantiating class {{class}} " . 
								"from callable, with the following message: {{message}}", [
								'class' => Text::stringify($class, null, ['quote_strings' => true]),
								'exception' => Text::stringify(get_class($exception), null, ['quote_strings' => true]),
								'message' => Text::uncapitalize($exception->getMessage(), true)
							]
						)
					]);
				}
			}
		}
		
		//object or class
		$object_class = $value;
		if (!is_object($object_class)) {
			$object_class = self::class($object_class, true);
			if (!isset($object_class)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INVALID,
					'error_message' => "Only the following types and formats can be coerced " . 
						"into an object or class:\n" . 
						" - a class string or object;\n" . 
						" - an integer with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\IntegerInstantiable\" interface;\n" . 
						" - a float with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\FloatInstantiable\" interface;\n" . 
						" - a string with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\StringInstantiable\" interface;\n" . 
						" - an array with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\ArrayInstantiable\" interface;\n" . 
						" - a callable with an <\$object_class_interface> implementing " . 
						"the \"Dracodeum\\Kit\\Interfaces\\CallableInstantiable\" interface."
				]);
			}
		}
		
		//object, class or interface
		if (isset($object_class_interface)) {
			$interface = is_string($object_class_interface) ? self::interface($object_class_interface, true) : null;
			if (isset($interface) && !self::implements($object_class, $interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only an object or class which implements {{interface}} is allowed.",
						['interface' => Text::stringify($interface, null, ['quote_strings' => true])]
					)
				]);
			} elseif (!isset($interface) && !self::isA($object_class, $object_class_interface)) {
				if ($no_throw) {
					return false;
				}
				throw new Exceptions\ObjectClassCoercionFailed([
					'value' => $value,
					'error_code' => Exceptions\ObjectClassCoercionFailed::ERROR_CODE_INVALID_CLASS,
					'error_message' => Text::fill(
						"Only an object or class which is or extends from {{base_class}} is allowed.", [
							'base_class' => Text::stringify(
								self::class($object_class_interface), null, ['quote_strings' => true]
							)
						]
					)
				]);
			}
		}
		
		//finalize
		$value = $object_class;
		return true;
	}
	
	/**
	 * Check if a given class or interface exists.
	 * 
	 * @param string $class_interface
	 * <p>The class or interface to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given class or interface exists.</p>
	 */
	final public static function exists(string $class_interface): bool
	{
		return class_exists($class_interface) || interface_exists($class_interface);
	}
	
	/**
	 * Check if a given class is instantiable.
	 * 
	 * @param string $class
	 * <p>The class to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given class is instantiable.</p>
	 */
	final public static function instantiable(string $class): bool
	{
		$class = self::class($class);
		return (new \ReflectionClass($class))->isInstantiable() && !self::implements($class, IUninstantiable::class);
	}
	
	/**
	 * Instantiate a new instance from a given object or class.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to instantiate from.</p>
	 * @param mixed ...$arguments
	 * <p>The arguments to instantiate with.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\UninstantiableClass
	 * @return object
	 * <p>The new instance from the given object or class.</p>
	 */
	final public static function instantiate($object_class, ...$arguments): object
	{
		$class = self::class($object_class);
		if (!self::instantiable($class)) {
			throw new Exceptions\UninstantiableClass([$class]);
		}
		return new $class(...$arguments);
	}
	
	/**
	 * Check if a given object is cloneable.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is cloneable.</p>
	 */
	final public static function cloneable(object $object): bool
	{
		return !($object instanceof IUncloneable || $object instanceof \Exception);
	}
	
	/**
	 * Clone a given object.
	 * 
	 * @param object $object
	 * <p>The object to clone.</p>
	 * @param bool $recursive [default = false]
	 * <p>Clone all the possible referenced subobjects into new instances recursively (if applicable).</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\UncloneableObject
	 * @return object
	 * <p>The cloned object from the given one.</p>
	 */
	final public static function clone(object $object, bool $recursive = false): object
	{
		if (!self::cloneable($object)) {
			throw new Exceptions\UncloneableObject([$object]);
		} elseif ($object instanceof ICloneable) {
			return $object->clone($recursive);
		}
		return clone $object;
	}
	
	/**
	 * Clone a given value.
	 * 
	 * If the given value is a cloneable object, then a clone of it is returned.<br>
	 * If the given value is an array and <var>$recursive</var> is set to boolean <code>true</code>, 
	 * then it is transversed recursively, with every cloneable object found being cloned and returned.<br>
	 * <br>
	 * For any other case, the given value is returned as is.
	 * 
	 * @param mixed $value
	 * <p>The value to clone.</p>
	 * @param bool $recursive [default = false]
	 * <p>Clone all the possible referenced subobjects into new instances recursively (if applicable).</p>
	 * @return mixed
	 * <p>The cloned value from the given one if applicable, or the given value if otherwise.</p>
	 */
	final public static function cloneValue($value, bool $recursive = false)
	{
		if (is_object($value) && self::cloneable($value)) {
			return self::clone($value, $recursive);
		} elseif ($recursive && is_array($value)) {
			foreach ($value as &$v) {
				$v = self::cloneValue($v, $recursive);
			}
			unset($v);
		}
		return $value;
	}
	
	/**
	 * Check if a given object is read-only-able.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is read-only-able.</p>
	 */
	final public static function readonlyable(object $object): bool
	{
		return $object instanceof IReadonlyable;
	}
	
	/**
	 * Check if a given object is read-only.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @param bool $recursive [default = false]
	 * <p>Check if the given object has been recursively set as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is read-only.</p>
	 */
	final public static function readonly(object $object, bool $recursive = false): bool
	{
		return $object instanceof IReadonlyable ? $object->isReadonly($recursive) : false;
	}
	
	/**
	 * Check if a given value is read-only.
	 * 
	 * If the given value is a read-only-able object, then it is checked.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every read-only-able object found being checked.<br>
	 * <br>
	 * For any other case, the given value is assumed to not be read-only.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @param bool $recursive [default = false]
	 * <p>Check if the given value has been recursively set as read-only.</p>
	 * @param bool $readonlyables_only [default = false]
	 * <p>Check read-only-able values only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is read-only.</p>
	 */
	final public static function readonlyValue($value, bool $recursive = false, bool $readonlyables_only = false): bool
	{
		if (is_object($value) && $value instanceof IReadonlyable) {
			return $value->isReadonly($recursive);
		} elseif ($recursive && Data::evaluate($value)) {
			foreach ($value as $v) {
				$readonlyable = is_object($v) && self::readonlyable($v);
				if ((!$readonlyables_only || $readonlyable) && !self::readonlyValue($v, true, $readonlyables_only)) {
					return false;
				}
			}
			return true;
		}
		return $readonlyables_only;
	}
	
	/**
	 * Set a given object as read-only.
	 * 
	 * @param object $object
	 * <p>The object to set as read-only.</p>
	 * @param bool $recursive [default = false]
	 * <p>Set all the possible referenced subobjects as read-only recursively (if applicable).</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\UnreadonlyableObject
	 * @return void
	 */
	final public static function setAsReadonly(object $object, bool $recursive = false): void
	{
		if ($object instanceof IReadonlyable) {
			$object->setAsReadonly($recursive);
			return;
		}
		throw new Exceptions\UnreadonlyableObject([$object]);
	}
	
	/**
	 * Set a given value as read-only.
	 * 
	 * If the given value is a read-only-able object, then it is set as read-only.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every read-only-able object found being set as read-only.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to set as read-only.</p>
	 * @param bool $recursive [default = false]
	 * <p>Set all the possible referenced subobjects as read-only recursively (if applicable).</p>
	 * @return void
	 */
	final public static function setValueAsReadonly($value, bool $recursive = false): void
	{
		if (is_object($value) && $value instanceof IReadonlyable) {
			$value->setAsReadonly($recursive);
		} elseif ($recursive && Data::evaluate($value)) {
			foreach ($value as $v) {
				self::setValueAsReadonly($v, $recursive);
			}
		}
	}
	
	/**
	 * Check if a given object is key-able.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is key-able.</p>
	 */
	final public static function keyable(object $object): bool
	{
		return $object instanceof IKeyable;
	}
	
	/**
	 * Cast a given object to a key.
	 * 
	 * @param object $object
	 * <p>The object to cast.</p>
	 * @param bool $recursive [default = false]
	 * <p>Cast the given object to a recursive key with all the possible referenced subobjects (if applicable).</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string
	 * <p>The given object cast to a key.</p>
	 */
	final public static function key(object $object, bool $recursive = false, ?bool &$safe = null): string
	{
		$safe = null;
		return $object instanceof IKeyable ? $object->toKey($recursive, $safe) : Data::keyfy($object, $safe);
	}
	
	/**
	 * Cast a given value to a key.
	 * 
	 * If the given value is a key-able object, then it is cast to a key.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every key-able object found being cast to a key.<br>
	 * <br>
	 * For any other case, the given value is forcefully keyfied.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to cast.</p>
	 * @param bool $recursive [default = false]
	 * <p>Cast the given value to a recursive key with all the possible referenced subobjects (if applicable).</p>
	 * @param bool $keyables_only [default = false]
	 * <p>Cast key-able values only.</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string|null
	 * <p>The given value cast to a key.<br>
	 * If <var>$keyables_only</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be cast to a key.</p>
	 */
	final public static function keyValue(
		$value, bool $recursive = false, bool $keyables_only = false, ?bool &$safe = null
	): ?string
	{
		$safe = null;
		if (is_object($value) && $value instanceof IKeyable) {
			return $value->toKey($recursive, $safe);
		} elseif ($recursive && Data::evaluate($value)) {
			//array
			$array = [];
			$array_safe = true;
			foreach ($value as $k => $v) {
				if (!$keyables_only || (is_object($v) && self::keyable($v))) {
					$key = self::keyValue($v, true, $keyables_only, $s);
					if ($key !== null) {
						$array[$k] = $key;
						$array_safe = $array_safe && $s;
					}
				}
			}
			
			//finalize
			if ($keyables_only && empty($array)) {
				return null;
			}
			$safe = $array_safe;
			return 'rK' . Data::keyfy(json_encode($array));
		}
		return $keyables_only ? null : Data::keyfy($value, $safe);
	}
	
	/**
	 * Check if a given object is persistable.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is persistable.</p>
	 */
	final public static function persistable(object $object): bool
	{
		return $object instanceof IPersistable;
	}
	
	/**
	 * Check if a given object has already been persisted at least once.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object has already been persisted at least once.</p>
	 */
	final public static function persisted(object $object): bool
	{
		return $object instanceof IPersistable ? $object->isPersisted() : false;
	}
	
	/**
	 * Check if a given value has already been persisted at least once.
	 * 
	 * If the given value is a persistable object, then it is checked.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every persistable object found being checked.<br>
	 * <br>
	 * For any other case, the given value is assumed to not have been persisted.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @param bool $recursive [default = false]
	 * <p>Check if the given value has already been recursively persisted at least once.</p>
	 * @param bool $persistables_only [default = false]
	 * <p>Check persistable values only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value has already been persisted at least once.</p>
	 */
	final public static function persistedValue($value, bool $recursive = false, bool $persistables_only = false): bool
	{
		if (is_object($value) && $value instanceof IPersistable) {
			return $value->isPersisted();
		} elseif ($recursive && Data::evaluate($value)) {
			foreach ($value as $v) {
				$persistable = is_object($v) && self::persistable($v);
				if ((!$persistables_only || $persistable) && !self::persistedValue($v, true, $persistables_only)) {
					return false;
				}
			}
			return true;
		}
		return $persistables_only;
	}
	
	/**
	 * Persist a given object.
	 * 
	 * @param object $object
	 * <p>The object to persist.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\NotPersistableObject
	 * @return void
	 */
	final public static function persist(object $object): void
	{
		if ($object instanceof IPersistable) {
			$object->persist();
		} else {
			throw new Exceptions\NotPersistableObject([$object]);
		}
	}
	
	/**
	 * Persist a given value.
	 * 
	 * If the given value is a persistable object, then it is persisted.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every persistable object found being persisted.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to persist.</p>
	 * @param bool $recursive [default = false]
	 * <p>Persist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return void
	 */
	final public static function persistValue($value, bool $recursive = false): void
	{
		if (is_object($value) && $value instanceof IPersistable) {
			$value->persist();
		} elseif ($recursive && Data::evaluate($value)) {
			foreach ($value as $v) {
				self::persistValue($v, true);
			}
		}
	}
	
	/**
	 * Check if a given object is unpersistable.
	 * 
	 * @param object $object
	 * <p>The object to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is unpersistable.</p>
	 */
	final public static function unpersistable(object $object): bool
	{
		return $object instanceof IUnpersistable;
	}
	
	/**
	 * Unpersist a given object.
	 * 
	 * @param object $object
	 * <p>The object to unpersist.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\NotUnpersistableObject
	 * @return void
	 */
	final public static function unpersist(object $object): void
	{
		if ($object instanceof IUnpersistable) {
			$object->unpersist();
		} else {
			throw new Exceptions\NotUnpersistableObject([$object]);
		}
	}
	
	/**
	 * Unpersist a given value.
	 * 
	 * If the given value is an unpersistable object, then it is unpersisted.<br>
	 * If the given value is an array or an object implementing the <code>Dracodeum\Kit\Interfaces\Arrayable</code> 
	 * interface, and <var>$recursive</var> is set to boolean <code>true</code>, then it is transversed recursively, 
	 * with every unpersistable object found being unpersisted.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Arrayable
	 * @param mixed $value
	 * <p>The value to unpersist.</p>
	 * @param bool $recursive [default = false]
	 * <p>Unpersist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return void
	 */
	final public static function unpersistValue($value, bool $recursive = false): void
	{
		if (is_object($value) && $value instanceof IUnpersistable) {
			$value->unpersist();
		} elseif ($recursive && Data::evaluate($value)) {
			foreach ($value as $v) {
				self::unpersistValue($v, true);
			}
		}
	}
	
	/**
	 * Check if a given object or class extends from or is of the same class as a given base object or class.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @param object|string $base_object_class
	 * <p>The base object or class to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class extends from or 
	 * is of the same class as the given base object or class.</p>
	 */
	final public static function isA($object_class, $base_object_class): bool
	{
		return is_a(
			is_object($object_class) ? $object_class : self::class($object_class),
			self::class($base_object_class), true
		);
	}
	
	/**
	 * Check if all given objects or classes extend from or are of the same class as a given base object or class.
	 * 
	 * @param object[]|string[] $objects_classes
	 * <p>The objects or classes to check.</p>
	 * @param object|string $base_object_class
	 * <p>The base object or class to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if all the given objects or classes extend from or
	 * are of the same class as the given base object or class.</p>
	 */
	final public static function areA(array $objects_classes, $base_object_class): bool
	{
		$base_class = self::class($base_object_class);
		foreach ($objects_classes as $object_class) {
			if (!is_a(is_object($object_class) ? $object_class : self::class($object_class), $base_class, true)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if a given object or class extends from or is of the same class as any given base objects or classes.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @param object[]|string[] $base_objects_classes
	 * <p>The base objects or classes to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class extends from or
	 * is of the same class as any of the given base objects or classes.</p>
	 */
	final public static function isAny($object_class, array $base_objects_classes): bool
	{
		if (!is_object($object_class)) {
			$object_class = self::class($object_class);
		}
		foreach ($base_objects_classes as $base_object_class) {
			if (is_a($object_class, self::class($base_object_class), true)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check if all given objects or classes extend from or are of the same class as any given base objects or classes.
	 * 
	 * @param object[]|string[] $objects_classes
	 * <p>The objects or classes to check.</p>
	 * @param object[]|string[] $base_objects_classes
	 * <p>The base objects or classes to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given objects or classes extend from or
	 * are of the same class as any of the given base objects or classes.</p>
	 */
	final public static function areAny(array $objects_classes, array $base_objects_classes): bool
	{
		//objects and classes
		foreach ($objects_classes as &$object_class) {
			if (!is_object($object_class)) {
				$object_class = self::class($object_class);
			}
		}
		unset($object_class);
		
		//base classes
		$base_classes = [];
		foreach ($base_objects_classes as $base_object_class) {
			$base_classes[] = self::class($base_object_class);
		}
		
		//check
		foreach ($objects_classes as $object_class) {
			$is_any = false;
			foreach ($base_classes as $base_class) {
				if (is_a($object_class, $base_class, true)) {
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
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @param string $interface
	 * <p>The interface to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class implements the given interface.</p>
	 */
	final public static function implements($object_class, string $interface): bool
	{
		return is_a(
			is_object($object_class) ? $object_class : self::class($object_class), self::interface($interface), true
		);
	}
	
	/**
	 * Check if a given object or class implements any given interfaces.
	 * 
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @param string[] $interfaces
	 * <p>The interfaces to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class implements any of the given interfaces.</p>
	 */
	final public static function implementsAny($object_class, array $interfaces): bool
	{
		if (!is_object($object_class)) {
			$object_class = self::class($object_class);
		}
		foreach ($interfaces as $interface) {
			if (is_a($object_class, self::interface($interface), true)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Check if a given object or class implements all given interfaces.
	 * 
	 * @see https://php.net/manual/en/language.oop5.interfaces.php
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @param string[] $interfaces
	 * <p>The interfaces to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class implements all the given interfaces.</p>
	 */
	final public static function implementsAll($object_class, array $interfaces): bool
	{
		if (!is_object($object_class)) {
			$object_class = self::class($object_class);
		}
		foreach ($interfaces as $interface) {
			if (!is_a($object_class, self::interface($interface), true)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if a given object or class is anonymous.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object or class is anonymous.</p>
	 */
	final public static function anonymous($object_class): bool
	{
		return preg_match('/^class@anonymous/', self::class($object_class));
	}
	
	/**
	 * Get class from a given object or class.
	 * 
	 * The leading backslash character <samp>\</samp> is never prepended to the returned class.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\InvalidObjectClass
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\ClassNotFound
	 * @return string|null
	 * <p>The class from the given object or class.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be retrieved.</p>
	 */
	final public static function class($object_class, bool $no_throw = false): ?string
	{
		if (is_object($object_class)) {
			return get_class($object_class);
		} elseif (!is_string($object_class)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\InvalidObjectClass([$object_class]);
		} elseif (!class_exists($object_class)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\ClassNotFound([$object_class]);
		}
		return $object_class[0] === '\\' ? substr($object_class, 1) : $object_class;
	}
	
	/**
	 * Validate and sanitize a given interface.
	 * 
	 * The leading backslash character <samp>\</samp> is never prepended to the returned interface.
	 * 
	 * @param string $interface
	 * <p>The interface to validate and sanitize.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\InterfaceNotFound
	 * @return string|null
	 * <p>The given interface validated and sanitized.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it was not found.</p>
	 */
	final public static function interface(string $interface, bool $no_throw = false): ?string
	{
		if (!interface_exists($interface)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\InterfaceNotFound([$interface]);
		}
		return $interface[0] === '\\' ? substr($interface, 1) : $interface;
	}
	
	/**
	 * Get short name from a given object, class or interface.
	 * 
	 * The returning short name is the class or interface name without its namespace.
	 * 
	 * @param object|string $object_class_interface
	 * <p>The object, class or interface to get from.</p>
	 * @return string
	 * <p>The short name from the given object, class or interface.</p>
	 */
	final public static function shortname($object_class_interface): string
	{
		$class_interface = self::interface($object_class_interface, true) ?? self::class($object_class_interface);
		return (new \ReflectionClass($class_interface))->getShortName();
	}
	
	/**
	 * Get namespace from a given object or class.
	 * 
	 * The returning namespace does not have the leading backslash character <samp>\</samp>, 
	 * thus an empty namespace is returned for the global one.
	 * 
	 * @see https://php.net/manual/en/language.namespaces.php
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @param int|null $depth [default = null]
	 * <p>The depth limit to get with.<br>
	 * If set to a number less than <code>0</code>, 
	 * then the limit is applied backwards (starting at the end of the namespace).<br>
	 * If not set, then no limit is applied.</p>
	 * @return string
	 * <p>The namespace from the given object or class.</p>
	 */
	final public static function namespace($object_class, ?int $depth = null): string
	{
		$namespace = (new \ReflectionClass(self::class($object_class)))->getNamespaceName();
		if (isset($depth)) {
			$nameparts = explode('\\', $namespace);
			$namespace = implode(
				'\\', $depth >= 0 ? array_slice($nameparts, 0, $depth) : array_slice($nameparts, $depth)
			);
		}
		return $namespace;
	}
	
	/**
	 * Get filename from a given object or class.
	 * 
	 * The returning filename is the absolute file path in the filesystem where the class is declared.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @return string|null
	 * <p>The filename from the given object or class or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function filename($object_class): ?string
	{
		$filename = (new \ReflectionClass(self::class($object_class)))->getFileName();
		return $filename === false ? null : $filename;
	}
	
	/**
	 * Get directory from a given object or class.
	 * 
	 * The returning directory is the absolute directory path in the filesystem where the class is declared.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @return string|null
	 * <p>The directory from the given object or class 
	 * or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function directory($object_class): ?string
	{
		$filename = self::filename($object_class);
		return isset($filename) ? dirname($filename) : null;
	}
	
	/**
	 * Get basename from a given object or class.
	 * 
	 * The returning basename is the complete name of the file where the class is declared.
	 * 
	 * @param object|string $object_class
	 * <p>The object or class to get from.</p>
	 * @return string|null
	 * <p>The basename from the given object or class or <code>null</code> if the class is not declared in any file.</p>
	 */
	final public static function basename($object_class): ?string
	{
		$filename = self::filename($object_class);
		return isset($filename) ? basename($filename) : null;
	}
}
