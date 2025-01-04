<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Type\{
	Info,
	Options,
	Exceptions
};
use Dracodeum\Kit\Utilities\Type\Info\Enums\Kind as EInfoKind;
use Dracodeum\Kit\Interfaces\{
	Integerable as IIntegerable,
	Floatable as IFloatable,
	Stringable as IStringable,
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
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Enums\Error\Type as EErrorType;
use Dracodeum\Kit\Primitives\{
	Error,
	Text as TextPrimitive
};
use LogicException;

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
	
	/** Normalize into a short name by removing the full namespace (flag). */
	public const NORMALIZE_SHORT_NAME = 0x01;
	
	/** Normalize with the global namespace leading backslash (flag). */
	public const NORMALIZE_LEADING_BACKSLASH = 0x02;
	
	
	
	//Private constants
	/** Inner delimiter character for types and parameters. */
	private const INNER_DELIMITER = ',';
	
	/** Regular expression pattern of the character set which must be followed by colon (:) in flags. */
	private const FLAGS_REQUIRED_COLON_CHARS_PATTERN = '\w.,:;"\'\\\\\/\[\](){}<>&|';
	
	/** Regular expression pattern of the character set which must be escaped or enclosed in a parameter value. */
	private const PARAMETER_VALUE_ESCAPABLE_CHARS_PATTERN = self::INNER_DELIMITER . ':()<>\\\\"';
	
	/** Phpfy non-associative array maximum pretty output horizontal length. */
	private const PHPFY_NONASSOC_ARRAY_PRETTY_MAX_HORIZONTAL_LENGTH = 50;

	
	
	//Final public static methods
	/**
	 * Check if a given class, interface or enumeration name exists.
	 * 
	 * @param string $name
	 * The class, interface or enumeration name to check.
	 * 
	 * @return bool
	 * Boolean `true` if the given class, interface or enumeration name exists.
	 */
	final public static function exists(string $name): bool
	{
		static $map = [];
		if (!isset($map[$name])) {
			$map[$name] = class_exists($name) || interface_exists($name) || enum_exists($name);
		}
		return $map[$name];
	}
	
	/**
	 * Get info instance from a given name.
	 * 
	 * @param string $name
	 * The name to get from.
	 * 
	 * @param bool $degroup
	 * Return an info instance with the given name already degrouped.
	 * 
	 * @param \Dracodeum\Kit\Enums\Error\Type $error_type
	 * The type of error to return if an error occurs.
	 * 
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\Info\InvalidName
	 * 
	 * @return \Dracodeum\Kit\Utilities\Type\Info|\Dracodeum\Kit\Primitives\Error|null
	 * An info instance from the given name.  
	 * If an error occurs, then the returning value follows the behavior set through `$error_type` instead.
	 */
	final public static function info(
		string $name, bool $degroup = false, EErrorType $error_type = EErrorType::THROWABLE
	): Info|Error|null
	{
		//name
		$name = trim($name);
		if ($name === '') {
			return match ($error_type) {
				EErrorType::NULL => null,
				default => $error_type->handleThrowable(
					new Exceptions\Info\InvalidName([$name, 'error_message' => "Cannot be empty."])
				)
			};
		}
		
		//memoization
		static $infos = [];
		if (isset($infos[$name][$degroup])) {
			return $infos[$name][$degroup];
		}
		
		//initialize
		static $type_delimiter = self::INNER_DELIMITER;
		static $union_delimiter = '|';
		static $intersection_delimiter = '&';
		static $parameter_delimiter = self::INNER_DELIMITER;
		static $split_flags = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
		
		//patterns
		static $name_pattern = '[a-z_]\w*(?:\.[a-z_]\w*)*';
		static $base_pattern = "(?:{$name_pattern}|\\\\?[a-z_]\w*(?:\\\\[a-z_]\w*)*)";
		static $flags_pattern = '(?:(?:\w\s*)+:)?(?:[^' . self::FLAGS_REQUIRED_COLON_CHARS_PATTERN . ']+)?';
		static $quoted_pattern = '"(?:\\\\.|[^"])*"';
		static $unnested_pattern = "(?:{$quoted_pattern}|\\\\.|[^()<>\"])";
		static $parameter_value_pattern = "(?:{$quoted_pattern}|(?:\\\\.|[^" .
			self::PARAMETER_VALUE_ESCAPABLE_CHARS_PATTERN . "])+)";
		static $parameter_pattern = "(?:{$name_pattern}\s*:\s*)?{$parameter_value_pattern}";
		static $parameters_pattern = "{$parameter_pattern}(?:\s*{$parameter_delimiter}\s*{$parameter_pattern})*";
		
		//match patterns
		static $match_array_pattern = '/\S\s*(\[\s*(?P<size>\d+)?\s*\])$/';
		static $match_group_pattern = "/^(\((?:{$quoted_pattern}|\\\\.|[^()\"]|(?1))*\))$/si";
		static $match_generic_pattern = "/^" .
			"(?P<flags>{$flags_pattern})?" .
			"(?P<base>{$base_pattern})" .
			"(?:\s*\(\s*(?P<parameters>{$parameters_pattern})?\s*\))?" .
			"(?:\s*<(?P<types>.+)>)?" .
			"$/si";
		static $match_type_non_pattern = "/^(?:\(\s*(?!\))|[{$type_delimiter})<>\"])+$/";
		static $match_parameter_pattern = "/^(?:(?P<name>{$name_pattern})\s*:\s*)?" .
			"(?P<value>{$parameter_value_pattern})$/si";
		
		//split patterns
		static $split_parameter_pattern = "/\s*({$parameter_pattern})\s*/si";
		static $split_delimiter_patterns = [];
		if (!$split_delimiter_patterns) {
			foreach ([$type_delimiter, $union_delimiter, $intersection_delimiter] as $delimiter) {
				$split_delimiter_patterns[$delimiter] = "/((?:" .
					"(?:{$quoted_pattern}|\\\\.|[^{$delimiter}()<>\"])+|" .
					"(\((?:{$unnested_pattern}|(?2)|(?3))*\))|" .
					"(<(?:{$unnested_pattern}|(?2)|(?3))*>)" .
				")+)/si";
			}
		}
		static $split_type_pattern = $split_delimiter_patterns[$type_delimiter];
		
		//group
		if (preg_match($match_group_pattern, $name)) {
			//name
			$group_name = trim(substr($name, 1, -1));
			if ($group_name === '') {
				return match ($error_type) {
					EErrorType::NULL => null,
					default => $error_type->handleThrowable(
						new Exceptions\Info\InvalidName([$name, 'error_message' => "Cannot have an empty group."])
					)
				};
			}
			
			//info
			$info = $degroup ? self::info($group_name, true, $error_type) : new Info(EInfoKind::GROUP, [$group_name]);
			if ($info instanceof Info) {
				$infos[$name][$degroup] = $info;
			}
			return $info;
		}
		
		//union and intersection
		static $delimiters_kinds = [
			$union_delimiter => EInfoKind::UNION,
			$intersection_delimiter => EInfoKind::INTERSECTION
		];
		foreach ($delimiters_kinds as $delimiter => $kind) {
			//process
			$names = [];
			$delimited = true;
			$split_pattern = $split_delimiter_patterns[$delimiter];
			foreach (preg_split($split_pattern, $name, flags: $split_flags) as $s) {
				$s = trim($s);
				if ($s === '' || ($s === $delimiter && $delimited)) {
					return match ($error_type) {
						EErrorType::NULL => null,
						default => $error_type->handleThrowable(
							new Exceptions\Info\InvalidName([
								$name, 'error_message' => "Cannot have blanks within a union or intersection."
							])
						)
					};
				} elseif ($s === $delimiter) {
					$delimited = true;
				} elseif ($delimited) {
					$names[] = $s;
					$delimited = false;
				}
			}
			
			//error
			if ($delimited && $names) {
				return match ($error_type) {
					EErrorType::NULL => null,
					default => $error_type->handleThrowable(
						new Exceptions\Info\InvalidName([
							$name,
							'error_message' => "Cannot have a union or intersection ending with a trailing operator."
						])
					)
				};
			}
			
			//finalize
			if (count($names) > 1) {
				$info = $infos[$name][false] = $infos[$name][true] = new Info($kind, $names);
				return $info;
			}
		}
		
		//array
		if (preg_match($match_array_pattern, $name, $matches)) {
			//parameters
			$parameters = [];
			$size = $matches['size'] ?? '';
			if ($size !== '') {
				$parameters[] = (int)$size;
			}
			
			//finalize
			$info = $infos[$name][false] = $infos[$name][true] = new Info(
				EInfoKind::ARRAY, [trim(substr($name, 0, -strlen($matches[1])))], parameters: $parameters
			);
			return $info;
		}
		
		//generic
		if (preg_match($match_generic_pattern, $name, $matches)) {
			//initialize
			$names = [$matches['base']];
			
			//flags
			$flags = preg_replace('/[\s:]/', '', $matches['flags'] ?? '');
			if (strlen(count_chars($flags, 3)) !== strlen($flags)) {
				return match ($error_type) {
					EErrorType::NULL => null,
					default => $error_type->handleThrowable(
						new Exceptions\Info\InvalidName([$name, 'error_message' => "Cannot have duplicated flags."])
					)
				};
			}
			
			//parameters
			$parameters = [];
			$delimited = true;
			foreach (preg_split($split_parameter_pattern, $matches['parameters'] ?? '', flags: $split_flags) as $s) {
				$s = trim($s);
				if ($s === '' || ($s === $parameter_delimiter && $delimited)) {
					return match ($error_type) {
						EErrorType::NULL => null,
						default => $error_type->handleThrowable(
							new Exceptions\Info\InvalidName([$name, 'error_message' => "Cannot have blank parameters."])
						)
					};
				} elseif ($s === $parameter_delimiter) {
					$delimited = true;
				} elseif ($delimited) {
					if (preg_match($match_parameter_pattern, $s, $parameter_matches)) {
						//initialize
						$parameter_name = trim($parameter_matches['name'] ?? '');
						$parameter_value = trim($parameter_matches['value']);
						if ($parameter_value[0] === '"') {
							$parameter_value = substr($parameter_value, 1, -1);
						}
						$parameter_value = stripcslashes($parameter_value);
						
						//parameter
						if ($parameter_name !== '') {
							if (isset($parameters[$parameter_name])) {
								return match ($error_type) {
									EErrorType::NULL => null,
									default => $error_type->handleThrowable(
										new Exceptions\Info\InvalidName([
											$name, 'error_message' => "Cannot have duplicated parameters."
										])
									)
								};
							}
							$parameters[$parameter_name] = $parameter_value;
						} else {
							$parameters[] = $parameter_value;
						}
						
						//finalize
						$delimited = false;
						unset($parameter_name, $parameter_value, $parameter_matches);
					} else {
						return match ($error_type) {
							EErrorType::NULL => null,
							default => $error_type->handleThrowable(
								new Exceptions\Info\InvalidName([
									$name, 'error_message' => "One or more malformed parameters were given."
								])
							)
						};
					}
				}
			}
			
			//parameters (finalize)
			ksort($parameters);
			if ($delimited && $parameters) {
				return match ($error_type) {
					EErrorType::NULL => null,
					default => $error_type->handleThrowable(
						new Exceptions\Info\InvalidName([
							$name, 'error_message' => "Cannot have parameters ending with a trailing comma."
						])
					)
				};
			}
			
			//types
			$delimited = true;
			foreach (preg_split($split_type_pattern, $matches['types'] ?? '', flags: $split_flags) as $s) {
				$s = trim($s);
				if ($s === '' || ($s === $type_delimiter && $delimited)) {
					return match ($error_type) {
						EErrorType::NULL => null,
						default => $error_type->handleThrowable(
							new Exceptions\Info\InvalidName([$name, 'error_message' => "Cannot have blank types."])
						)
					};
				} elseif ($s === $type_delimiter) {
					$delimited = true;
				} elseif (preg_match($match_type_non_pattern, $s)) {
					return match ($error_type) {
						EErrorType::NULL => null,
						default => $error_type->handleThrowable(
							new Exceptions\Info\InvalidName([
								$name, 'error_message' => "At least one malformed type was found."
							])
						)
					};
				} elseif ($delimited) {
					$names[] = $s;
					$delimited = false;
				}
			}
			
			//types (finalize)
			if ($delimited && isset($names[1])) {
				return match ($error_type) {
					EErrorType::NULL => null,
					default => $error_type->handleThrowable(
						new Exceptions\Info\InvalidName([
							$name, 'error_message' => "Cannot have types ending with a trailing comma."
						])
					)
				};
			}
			
			//return
			$info = $infos[$name][false] = $infos[$name][true] = new Info(
				EInfoKind::GENERIC, $names, $flags, $parameters
			);
			return $info;
		}
		
		//error
		return match ($error_type) {
			EErrorType::NULL => null,
			default => $error_type->handleThrowable(new Exceptions\Info\InvalidName([$name]))
		};
	}
	
	/**
	 * Normalize a given name.
	 * 
	 * @param string $name
	 * The name to normalize.
	 * 
	 * @param int $flags
	 * The flags to normalize with, as any combination of the following:
	 * - `NORMALIZE_SHORT_NAME` (*self*): return a short name by removing the full namespace;
	 * - `NORMALIZE_LEADING_BACKSLASH` (*self*): return with the global namespace leading backslash.
	 * 
	 * @return string
	 * The given name normalized.
	 */
	final public static function normalize(string $name, int $flags = 0x00): string
	{
		static $map = [];
		if (!isset($map[$name][$flags])) {
			$map[$name][$flags] = self::normalizeName($name, $flags);
		}
		return $map[$name][$flags];
	}
	
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
				Call::SOURCE_CONSTANTS_VALUES | Call::SOURCE_NO_MIXED_TYPES | Call::SOURCE_TYPES_LEADING_BACKSLASH
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
		} elseif ($value instanceof IIntegerable) {
			$value = $value->toInteger();
			return true;
		} elseif ($value instanceof IFloatable) {
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringable
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringable
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
	 * &nbsp; &#8226; &nbsp; an object implementing the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringable
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
			if ($value instanceof IStringable) {
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
				" - an object implementing the \"Dracodeum\\Kit\\Interfaces\\Stringable\" interface."
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
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\UncloneableObject
	 * @return object
	 * <p>The cloned object from the given one.</p>
	 */
	final public static function clone(object $object): object
	{
		if (!self::cloneable($object)) {
			throw new Exceptions\UncloneableObject([$object]);
		} elseif ($object instanceof ICloneable) {
			return $object->clone();
		}
		return clone $object;
	}
	
	/**
	 * Clone a given value.
	 * 
	 * If the given value is a cloneable object, then a clone of it is returned.<br>
	 * If the given value is an array, then it is transversed recursively with every cloneable object found being 
	 * cloned and returned.<br>
	 * <br>
	 * For any other case, the given value is returned as is.
	 * 
	 * @param mixed $value
	 * <p>The value to clone.</p>
	 * @return mixed
	 * <p>The cloned value from the given one if applicable, or the given value if otherwise.</p>
	 */
	final public static function cloneValue($value)
	{
		if (is_object($value) && self::cloneable($value)) {
			return self::clone($value);
		} elseif (is_array($value)) {
			foreach ($value as &$v) {
				$v = self::cloneValue($v);
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
	 * @return bool
	 * <p>Boolean <code>true</code> if the given object is read-only.</p>
	 */
	final public static function readonly(object $object): bool
	{
		return $object instanceof IReadonlyable ? $object->isReadonly() : false;
	}
	
	/**
	 * Check if a given value is read-only.
	 * 
	 * If the given value is a read-only-able object, then it is checked.<br>
	 * If the given value is an array, then it is transversed recursively with every read-only-able object found being 
	 * checked.<br>
	 * <br>
	 * For any other case, the given value is assumed to not be read-only.
	 * 
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @param bool $readonlyables_only [default = false]
	 * <p>Check read-only-able values only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is read-only.</p>
	 */
	final public static function readonlyValue($value, bool $readonlyables_only = false): bool
	{
		if ($value instanceof IReadonlyable) {
			return $value->isReadonly();
		} elseif (is_array($value)) {
			foreach ($value as $v) {
				$readonlyable = is_object($v) && self::readonlyable($v);
				if ((!$readonlyables_only || $readonlyable) && !self::readonlyValue($v, $readonlyables_only)) {
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
	 * @throws \Dracodeum\Kit\Utilities\Type\Exceptions\UnreadonlyableObject
	 */
	final public static function setAsReadonly(object $object): void
	{
		if ($object instanceof IReadonlyable) {
			$object->setAsReadonly();
			return;
		}
		throw new Exceptions\UnreadonlyableObject([$object]);
	}
	
	/**
	 * Set a given value as read-only.
	 * 
	 * If the given value is a read-only-able object, then it is set as read-only.<br>
	 * If the given value is an array, then it is transversed recursively with every read-only-able object found being 
	 * set as read-only.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @param mixed $value
	 * <p>The value to set as read-only.</p>
	 */
	final public static function setValueAsReadonly($value): void
	{
		if ($value instanceof IReadonlyable) {
			$value->setAsReadonly();
		} elseif (is_array($value)) {
			foreach ($value as $v) {
				self::setValueAsReadonly($v);
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
	 * <p>Cast the given object to a recursive key with all the possible referenced subobjects.</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string
	 * <p>A key cast from the given object.</p>
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
	 * <p>Cast the given value to a recursive key with all the possible referenced subobjects.</p>
	 * @param bool $keyables_only [default = false]
	 * <p>Cast key-able values only.</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string|null
	 * <p>A key cast from the given value.<br>
	 * If <var>$keyables_only</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be cast to a key.</p>
	 */
	final public static function keyValue(
		$value, bool $recursive = false, bool $keyables_only = false, ?bool &$safe = null
	): ?string
	{
		$safe = null;
		if ($value instanceof IKeyable) {
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
	 * If the given value is an array, then it is transversed recursively with every persistable object found being 
	 * checked.<br>
	 * <br>
	 * For any other case, the given value is assumed to not have been persisted.
	 * 
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @param bool $persistables_only [default = false]
	 * <p>Check persistable values only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value has already been persisted at least once.</p>
	 */
	final public static function persistedValue($value, bool $persistables_only = false): bool
	{
		if ($value instanceof IPersistable) {
			return $value->isPersisted();
		} elseif (is_array($value)) {
			foreach ($value as $v) {
				$persistable = is_object($v) && self::persistable($v);
				if ((!$persistables_only || $persistable) && !self::persistedValue($v, $persistables_only)) {
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
	 * If the given value is an array, then it is transversed recursively with every persistable object found being 
	 * persisted.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @param mixed $value
	 * <p>The value to persist.</p>
	 */
	final public static function persistValue($value): void
	{
		if ($value instanceof IPersistable) {
			$value->persist();
		} elseif (is_array($value)) {
			foreach ($value as $v) {
				self::persistValue($v);
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
	 * If the given value is an array, then it is transversed recursively with every unpersistable object found being 
	 * unpersisted.<br>
	 * <br>
	 * For any other case, the given value is left as is.
	 * 
	 * @param mixed $value
	 * <p>The value to unpersist.</p>
	 */
	final public static function unpersistValue($value): void
	{
		if ($value instanceof IUnpersistable) {
			$value->unpersist();
		} elseif (is_array($value)) {
			foreach ($value as $v) {
				self::unpersistValue($v);
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
	 * @param (object|string)[] $objects_classes
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
	 * @param (object|string)[] $base_objects_classes
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
	 * @param (object|string)[] $objects_classes
	 * <p>The objects or classes to check.</p>
	 * @param (object|string)[] $base_objects_classes
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
	
	/**
	 * Check if a given type is covariant in relation to a given base type.
	 * 
	 * @param string $type
	 * <p>The type to check.</p>
	 * @param string $base_type
	 * <p>The base type to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given type is covariant in relation to the given base type.</p>
	 */
	final public static function covariant(string $type, string $base_type): bool
	{
		static $map = [];
		if (!isset($map[$type][$base_type])) {
			//initialize
			$t_info = self::info($type, true);
			$bt_info = self::info($base_type, true);
			
			//generic
			if ($t_info->kind === EInfoKind::GENERIC && $bt_info->kind === $t_info->kind) {
				//initialize
				$t = self::normalize($t_info->name);
				$bt = self::normalize($bt_info->name);
				$t_count = count($t_info->names);
				$t_nullable = strpos($t_info->flags, '?') !== false;
				$bt_nullable = strpos($bt_info->flags, '?') !== false;
				$null_covariant = ($t === 'null' || $bt === 'null') && ($t_nullable || $bt_nullable);
				
				//covariant
				$covariant = $null_covariant || $bt === 'void' || ($bt === 'mixed' && $t !== 'void') || (
					$t_count === count($bt_info->names) && ($bt_nullable || !$t_nullable) && (
						$t === $bt || (
							self::exists($t) && ($bt === 'object' || (self::exists($bt) && is_a($t, $bt, true)))
						)
					)
				);
				
				//subtypes
				if ($covariant) {
					for ($i = 1; $i < $t_count; $i++) {
						if (!self::covariant($t_info->names[$i], $bt_info->names[$i])) {
							$covariant = false;
							break;
						}
					}
				}
				
				//finalize
				$map[$type][$base_type] = $covariant;
				
			//array
			} elseif ($t_info->kind === EInfoKind::ARRAY && $bt_info->kind === $t_info->kind) {
				$map[$type][$base_type] = self::covariant($t_info->name, $bt_info->name);
				
			//union and intersection (type)
			} elseif (in_array($t_info->kind, [EInfoKind::UNION, EInfoKind::INTERSECTION], true)) {
				$is_union = $t_info->kind === EInfoKind::UNION;
				$covariant = $is_union;
				foreach ($t_info->names as $t_name) {
					if (self::covariant($t_name, $base_type) !== $is_union) {
						$covariant = !$is_union;
						break;
					}
				}
				$map[$type][$base_type] = $covariant;
				
			//union and intersection (base type)
			} elseif (in_array($bt_info->kind, [EInfoKind::UNION, EInfoKind::INTERSECTION], true)) {
				$is_union = $bt_info->kind === EInfoKind::UNION;
				$covariant = !$is_union;
				foreach ($bt_info->names as $bt_name) {
					if (self::covariant($type, $bt_name) === $is_union) {
						$covariant = $is_union;
						break;
					}
				}
				$map[$type][$base_type] = $covariant;
			
			//other
			} else {
				$map[$type][$base_type] = false;
			}
		}
		return $map[$type][$base_type];
	}
	
	/**
	 * Check if a given type is contravariant in relation to a given base type.
	 * 
	 * @param string $type
	 * <p>The type to check.</p>
	 * @param string $base_type
	 * <p>The base type to check against.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given type is contravariant in relation to the given base type.</p>
	 */
	final public static function contravariant(string $type, string $base_type): bool
	{
		return self::covariant($base_type, $type);
	}
	
	/**
	 * Cast a given value.
	 * 
	 * @param mixed $value
	 * The value to cast.
	 * 
	 * @param coercible<\Dracodeum\Kit\Components\Type> $type
	 * The type to cast with.
	 * 
	 * @param array $properties
	 * The properties to cast with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
	 * The context to cast for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\CastFailed
	 * 
	 * @return mixed
	 * The given value cast.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the given value failed to be cast.
	 */
	final public static function cast(
		mixed $value, $type, array $properties = [], $context = EContext::INTERNAL, bool $no_throw = false
	): mixed
	{
		if (!($type instanceof Component)) {
			$type = Component::build($type, $properties);
		}
		return $type->processCast($value, $context, $no_throw);
	}
	
	/**
	 * Coerce a given value.
	 * 
	 * @param mixed $value
	 * The value to coerce.
	 * 
	 * @param coercible<\Dracodeum\Kit\Components\Type> $type
	 * The type to coerce with.
	 * 
	 * @param array $properties
	 * The properties to coerce with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
	 * The context to coerce for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\CoercionFailed
	 * 
	 * @return bool
	 * Boolean `true` is always returned if the given value is successfully coerced, otherwise an exception is thrown, 
	 * unless `$no_throw` is set to boolean `true`, in which case boolean `false` is returned instead.
	 */
	final public static function coerce(
		mixed &$value, $type, array $properties = [], $context = EContext::INTERNAL, bool $no_throw = false
	): bool
	{
		if (!($type instanceof Component)) {
			$type = Component::build($type, $properties);
		}
		return $type->processCoercion2($value, $context, $no_throw);
	}
	
	/**
	 * Textify a given value.
	 * 
	 * @param mixed $value
	 * The value to textify.
	 * 
	 * @param coercible<\Dracodeum\Kit\Components\Type>|null $type
	 * The type to textify with.
	 * 
	 * @param array $properties
	 * The properties to textify with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @param coercible<enum<\Dracodeum\Kit\Components\Type\Enumerations\Context>> $context
	 * The context to textify for.
	 * 
	 * @param bool $no_throw
	 * Do not throw an exception.
	 * 
	 * @throws \Dracodeum\Kit\Components\Type\Exceptions\TextificationFailed
	 * 
	 * @return \Dracodeum\Kit\Primitives\Text|null
	 * The given value textified, as a text instance.  
	 * If `$no_throw` is set to boolean `true`, then `null` is returned if the given value failed to be textified.
	 */
	final public static function textify(
		mixed $value, $type = null, array $properties = [], $context = EContext::INTERNAL, bool $no_throw = false
	): ?TextPrimitive
	{
		if (!($type instanceof Component)) {
			if ($type === null) {
				//type
				$type_name = gettype($value);
				$type = match ($type_name) {
					'boolean', 'integer', 'double', 'string', 'array', 'object', 'resource' => $type_name,
					'resource (closed)' => 'resource',
					default => 'mixed'
				};
				
				//array
				if ($type === 'array') {
					$properties += ['non_associative' => !Data::associative($value)];
				}
			}
			$type = Component::build($type, $properties);
		}
		return $type->textify($value, $context, $no_throw);
	}
	
	
	
	//Private static methods
	/**
	 * Normalize a given name.
	 * 
	 * @param string $name
	 * The name to normalize.
	 * 
	 * @param int $flags
	 * The flags to normalize with, as any combination of the following:
	 * - `NORMALIZE_SHORT_NAME` (*self*): return a short name by removing the full namespace;
	 * - `NORMALIZE_LEADING_BACKSLASH` (*self*): return with the global namespace leading backslash.
	 * 
	 * @param int $depth
	 * The current depth being normalized at.
	 * 
	 * @throws \LogicException
	 * 
	 * @return string
	 * The given name normalized.
	 */
	private static function normalizeName(string $name, int $flags = 0x00, int $depth = 0): string
	{
		//initialize
		$n_name = trim($name);
		if ($n_name === '') {
			$n_name = 'mixed';
		}
		
		//info
		$info = self::info($n_name);
		switch ($info->kind) {
			//generic
			case EInfoKind::GENERIC:
				//initialize
				$n_name = $info->name;
				if (!self::exists($n_name)) {
					$n_name = strtolower($n_name);
				} elseif ($flags & self::NORMALIZE_SHORT_NAME) {
					$n_name = self::shortname($n_name);
				} elseif ($flags & self::NORMALIZE_LEADING_BACKSLASH) {
					$n_name = '\\' . $n_name;
				}
				
				//names
				if (isset($info->names[1])) {
					$subtypes = [];
					for ($i = 1; $i < count($info->names); $i++) {
						$subtypes[] = self::normalizeName($info->names[$i], $flags, $depth + 1);
					}
					$n_name .= '<' . implode(',', $subtypes) . '>';
					unset($subtypes);
				}
				
				//flags
				$n_flags = $info->flags;
				static $flags_colon_chars_pattern = '/[' . self::FLAGS_REQUIRED_COLON_CHARS_PATTERN . ']/';
				if (preg_match_all($flags_colon_chars_pattern, $n_flags, $matches)) {
					$colon_flags = $matches[0];
					$non_colon_flags = array_keys(
						array_diff_key(array_flip(str_split($n_flags)), array_flip($colon_flags))
					);
					sort($colon_flags, SORT_STRING);
					sort($non_colon_flags, SORT_STRING);
					$n_flags = implode('', $colon_flags) . ':' . implode('', $non_colon_flags);
					unset($colon_flags, $non_colon_flags);
				}
				$n_name = $n_flags . $n_name;
				unset($n_flags, $matches);
				
				//parameters
				if ($info->parameters) {
					//initialize
					$n_parameters = [];
					static $parameter_value_escapable_chars_pattern = '/[' .
						self::PARAMETER_VALUE_ESCAPABLE_CHARS_PATTERN . ']/';
						
					//process
					$i = 0;
					foreach ($info->parameters as $k => $v) {
						//parameter
						$n_parameter = preg_match($parameter_value_escapable_chars_pattern, $v)
							? '"' . addcslashes($v, '"') . '"'
							: $v;
						
						//key
						if ($k !== $i++) {
							$n_parameter = $k . ':' . $n_parameter;
						}
						
						//finalize
						$n_parameters[] = $n_parameter;
						unset($n_parameter);
					}
					
					//finalize
					$n_name .= '(' . implode(',', $n_parameters) . ')';
					unset($n_parameters, $i, $k, $v);
				}
				
				//finalize
				break;
				
			//array
			case EInfoKind::ARRAY:
				$n_name = self::normalizeName($info->name, $flags, $depth + 1) . '[';
				if (isset($info->parameters[0])) {
					$n_name .= $info->parameters[0];
				}
				$n_name .= ']';
				break;
				
			//group
			case EInfoKind::GROUP:
				$n_name = self::normalizeName($info->name, $flags, $depth);
				$n_info = self::info($n_name);
				if ($depth > 0 && !in_array($n_info->kind, [EInfoKind::GENERIC, EInfoKind::GROUP], true)) {
					$n_name = '(' . $n_name . ')';
				}
				unset($n_info);
				break;
				
			//union or intersection
			case EInfoKind::UNION:
			case EInfoKind::INTERSECTION:
				//initialize
				$n_names = [];
				$is_union = $info->kind === EInfoKind::UNION;
				
				//normalize
				foreach ($info->names as $i_name) {
					$n_names[] = self::normalizeName($i_name, $flags, $depth + 1);
				}
				$n_names = array_values(array_unique($n_names, SORT_STRING));
				$n_name = implode($is_union ? '|' : '&', $n_names);
				unset($i_name);
				
				//nullable
				if (
					$is_union && count($n_names) === 2 && $n_names[0] !== $n_names[1] &&
					($n_names[0] === 'null' || $n_names[1] === 'null')
				) {
					$i_name = $n_names[$n_names[0] === 'null' ? 1 : 0];
					$i_info = self::info($i_name, true);
					if ($i_info->kind === EInfoKind::GENERIC) {
						if (strpos($i_info->flags, '?') === false) {
							$i_name = '?' . $i_name;
						}
						$n_name = self::normalizeName($i_name, $flags, $depth + 1);
					}
					unset($i_name, $i_info);
				}
				
				//finalize
				unset($n_names, $is_union);
				break;
				
			//default
			default:
				throw new LogicException("Unknown info kind \"{$info->kind}\".");
		}
		
		//return
		return $n_name;
	}
}
