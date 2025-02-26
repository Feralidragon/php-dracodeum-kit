<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Interfaces\{
	Properties as IProperties,
	Stringable as IStringable
};
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\Text\{
	Options,
	Exceptions
};
use Dracodeum\Kit\Root\Locale;

/** This utility implements a set of methods used to manipulate and get information from text and strings. */
final class Text extends Utility
{
	//Public constants
	/** Convert non-associative arrays into comma-separated lists during stringification. */
	public const STRING_NONASSOC_MODE_COMMA_LIST = 'COMMA_LIST';
	
	/**
	 * Convert non-associative arrays into comma-separated lists, 
	 * with an "and" conjunction for the last two elements, during stringification.
	 */
	public const STRING_NONASSOC_MODE_COMMA_LIST_AND = 'COMMA_LIST_AND';
	
	/**
	 * Convert non-associative arrays into comma-separated lists, 
	 * with an "or" conjunction for the last two elements, during stringification.
	 */
	public const STRING_NONASSOC_MODE_COMMA_LIST_OR = 'COMMA_LIST_OR';
	
	/**
	 * Convert non-associative arrays into comma-separated lists,
	 * with a "nor" conjunction for the last two elements, during stringification.
	 */
	public const STRING_NONASSOC_MODE_COMMA_LIST_NOR = 'COMMA_LIST_NOR';
	
	/** Capitalize first word during unslugification (flag). */
	public const UNSLUG_CAPITALIZE_FIRST = 0x01;
	
	/** Capitalize all words during unslugification (flag). */
	public const UNSLUG_CAPITALIZE_ALL = 0x02;
	
	/** Camel case notation. */
	public const CASE_CAMEL = 'camelCase';
	
	/** Pascal case notation. */
	public const CASE_PASCAL = 'PascalCase';
	
	/** Snake case notation. */
	public const CASE_SNAKE = 'snake_case';
	
	/** Kebab case notation. */
	public const CASE_KEBAB = 'kebab-case';
	
	/** Macro case notation. */
	public const CASE_MACRO = 'MACRO_CASE';
	
	/** Cobol case notation. */
	public const CASE_COBOL = 'COBOL-CASE';
	
	
	
	//Private constants
	/** Placeholder regular expression pattern. */
	private const PLACEHOLDER_PATTERN = '(?:[a-zA-Z_]\w*(?:\(\))?)(?:\.[a-zA-Z_]\w*(?:\(\))?)*';
	
	
	
	//Final public static methods
	/**
	 * Check if a given string is empty.
	 * 
	 * A given string is considered to be empty if its value is either <code>null</code> or <code>''</code>.
	 * 
	 * @param string|null $string
	 * <p>The string to check.</p>
	 * @param bool $ignore_whitespace [default = false]
	 * <p>Ignore whitespace characters from the given string.<br>
	 * These characters are defined as follows:<br>
	 * &nbsp; &#8226; &nbsp; space (<code>' '</code>);<br>
	 * &nbsp; &#8226; &nbsp; tab (<code>"\t"</code>);<br>
	 * &nbsp; &#8226; &nbsp; newline (<code>"\n"</code>);<br>
	 * &nbsp; &#8226; &nbsp; carriage return (<code>"\r"</code>);<br>
	 * &nbsp; &#8226; &nbsp; NUL-byte (<code>"\0"</code>);<br>
	 * &nbsp; &#8226; &nbsp; vertical tab (<code>"\x0B"</code>).</p>
	 * @param bool $unicode [default = false]
	 * <p>Check as a Unicode string.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is empty.</p>
	 */
	final public static function empty(?string $string, bool $ignore_whitespace = false, bool $unicode = false): bool
	{
		if ($string === null) {
			return true;
		} elseif ($ignore_whitespace) {
			return preg_match('/^\s*$/s' . ($unicode ? 'u' : ''), $string);
		}
		return $string === '';
	}
	
	/**
	 * Coalesce string from a given set of strings.
	 * 
	 * The returning string is the first one from the given set of strings which is not empty 
	 * (not <code>null</code> nor <code>''</code>).
	 * 
	 * @param (string|null)[] $strings
	 * <p>The strings to coalesce from.</p>
	 * @param (int|string)[] $keys [default = []]
	 * <p>The keys to coalesce by.<br>
	 * If empty, then all strings from the given set are used to coalesce by, 
	 * otherwise only the strings in the matching keys are used.<br>
	 * The order of these keys also establish the order of the coalesce operation.</p>
	 * @param bool $ignore_whitespace [default = false]
	 * <p>Ignore whitespace characters from the given strings.<br>
	 * These characters are defined as follows:<br>
	 * &nbsp; &#8226; &nbsp; space (<code>' '</code>);<br>
	 * &nbsp; &#8226; &nbsp; tab (<code>"\t"</code>);<br>
	 * &nbsp; &#8226; &nbsp; newline (<code>"\n"</code>);<br>
	 * &nbsp; &#8226; &nbsp; carriage return (<code>"\r"</code>);<br>
	 * &nbsp; &#8226; &nbsp; NUL-byte (<code>"\0"</code>);<br>
	 * &nbsp; &#8226; &nbsp; vertical tab (<code>"\x0B"</code>).</p>
	 * @param int|string|null $coalesced_key [reference output] [default = null]
	 * <p>The coalesced key corresponding to the returned string.</p>
	 * @return string|null
	 * <p>The coalesced string from the given set of strings or <code>null</code> if all strings are empty.</p>
	 */
	final public static function coalesce(
		array $strings, array $keys = [], bool $ignore_whitespace = false, &$coalesced_key = null
	): ?string
	{
		$coalesced_key = null;
		foreach (empty($keys) ? array_keys($strings) : $keys as $key) {
			if (isset($strings[$key]) && !self::empty($strings[$key], $ignore_whitespace)) {
				$coalesced_key = $key;
				return $strings[$key];
			}
		}
		return null;
	}
	
	/**
	 * Generate a string from a given value.
	 * 
	 * The returning string represents the given value in order to be shown or printed out in messages.<br>
	 * <br>
	 * Scalar values retain their full representation, while objects are represented only by their class names or ids, 
	 * resources by their ids, and arrays as lists or structures depending on whether they are associative.<br>
	 * <br>
	 * Objects implementing either the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface or 
	 * the <code>__toString</code> method are stringified through one of them, whichever one is implemented first, 
	 * with the former preferred over the latter.
	 * 
	 * @see https://php.net/manual/en/language.oop5.magic.php#object.tostring
	 * @see \Dracodeum\Kit\Interfaces\Stringable
	 * @param mixed $value
	 * <p>The value to generate from.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Stringify|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\Stringify\UnsupportedValueType
	 * @return string|null
	 * <p>The generated string from the given value.<br>
	 * If <var>$options->no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be generated.</p>
	 */
	final public static function stringify($value, $text_options = null, $options = null): ?string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		$options = Options\Stringify::coerce($options);
		$is_technical = $text_options->info_scope === EInfoScope::TECHNICAL;
		$is_enduser = $text_options->info_scope === EInfoScope::ENDUSER;
		$prepend_type = !$is_enduser && $options->prepend_type;
		
		//null
		if (!isset($value)) {
			if ($is_enduser) {
				/**
				 * @description Null value expression, as a text representation of null for the end-user.
				 * @tags end-user
				 */
				return self::localize("null", self::class, $text_options);
			}
			return 'null';
		}
		
		//boolean
		if (is_bool($value)) {
			if ($is_enduser) {
				//true
				if ($value) {
					/**
					 * @description Affirmative expression, \
					 * as a text representation of a boolean "true" for the end-user, as in "enabled" or "supported".
					 * @tags end-user
					 */
					return self::localize("yes", self::class, $text_options);
				}
				
				//false
				/**
				 * @description Negative expression, \
				 * as a text representation of a boolean "false" for the end-user, as in "disabled" or "unsupported".
				 * @tags end-user
				 */
				return self::localize("no", self::class, $text_options);
			}
			$string = $value ? 'true' : 'false';
			return $prepend_type ? '(boolean)' . strtolower($string) : $string;
		}
		
		//integer or float
		if (is_int($value) || is_float($value)) {
			$string = (string)$value;
			return $prepend_type ? (is_int($value) ? "(integer){$string}" : "(float){$string}") : $string;
		}
		
		//string
		if (is_string($value)) {
			$string = $value;
			if ($prepend_type || $options->quote_strings) {
				$string = $is_enduser ? "\u{201c}{$string}\u{201d}" : "\"{$string}\"";
			}
			return $prepend_type ? "(string){$string}" : $string;
		}
		
		//object
		if (is_object($value)) {
			//initialize
			$id = spl_object_id($value);
			$class = get_class($value);
			
			//stringable
			if (!$options->non_stringable) {
				if ($prepend_type && Data::evaluate($value)) {
					//initialize
					$k_options = $options->clone();
					$k_options->prepend_type = false;
					$k_options->quote_strings = true;
					
					//strings
					$strings = [];
					foreach ($value as $k => $v) {
						//key
						$k_string = self::stringify($k, $text_options, $k_options);
						if ($k_string === null) {
							return null;
						}
						
						//value
						$v_string = self::stringify($v, $text_options, $options);
						if ($v_string === null) {
							return null;
						}
						
						//finalize
						$strings[] = "{$k_string}: {$v_string}";
						unset($k_string, $v_string);
					}
					unset($k_options);
					
					//return
					return "(object){$class}#{$id} " . 
						(empty($strings) ? '{}' : "{\n" . self::indentate(implode("\n", $strings), 3, ' ') . "\n}");
					
				} elseif ($value instanceof IStringable) {
					$string = $value->toString($text_options);
					return $prepend_type ? "(object){$class}#{$id} {$string}" : $string;
				} elseif (Type::evaluateString($value)) {
					return $prepend_type ? "(object){$class}#{$id} {$value}" : $value;
				}
			}
			
			//stringify
			if ($is_enduser) {
				/**
				 * @description An internal object expression, as a text representation of an object for the end-user, 
				 * for whom only its id may be relevant for bug reporting purposes.
				 * @tags end-user
				 * @example object(294828143)
				 */
				return self::localize(
					"object({{id}})",
					self::class, $text_options, ['parameters' => ['id' => $id]]
				);
			} elseif ($is_technical) {
				return self::fill("object({{id}})", ['id' => $id]);
			}
			return $prepend_type ? "(object){$class}#{$id}" : "object({$class})#{$id}";
		}
		
		//resource
		if (is_resource($value) || gettype($value) === 'resource (closed)') {
			$id = (int)$value;
			if ($is_enduser) {
				/**
				 * @description An internal resource expression, \
				 * as a text representation of a resource for the end-user, \
				 * for whom only its id may be relevant for bug reporting purposes.
				 * @tags end-user
				 * @example resource(32)
				 */
				return self::localize(
					"resource({{id}})",
					self::class, $text_options, ['parameters' => ['id' => $id]]
				);
			} elseif ($is_technical) {
				return self::fill("resource({{id}})", ['id' => $id]);
			}
			return $prepend_type ? "(resource)#{$id}" : "resource({$id})";
		}
		
		//array
		if (is_array($value)) {
			//initialize
			$associative = $options->associative || Data::associative($value);
			$assoc_options = null;
			if ($associative) {
				$assoc_options = $options->clone();
				$assoc_options->prepend_type = false;
				$assoc_options->quote_strings = $options->prepend_type;
			}
			
			//strings
			$strings = [];
			foreach ($value as $k => $v) {
				$v_string = self::stringify($v, $text_options, $options);
				if (!isset($v_string)) {
					return null;
				} elseif ($associative) {
					$k_string = self::stringify($k, $text_options, $assoc_options);
					if (!isset($k_string)) {
						return null;
					}
					$v_string = "{$k_string}: {$v_string}";
					unset($k_string);
				}
				$strings[] = $v_string;
				unset($v_string);
			}
			
			//associative
			if ($associative) {
				$string = $is_enduser ? '' : '{}';
				if (!empty($strings)) {
					$string = "\n" . self::indentate(implode("\n", $strings), $is_enduser ? 2 : 3, ' ');
					if (!$is_enduser) {
						$string = "{{$string}\n}";
					}
				}
				return $prepend_type ? "(array){$string}" : $string;
			}
			
			//non-associative (initialize)
			$non_assoc_mode = $options->non_assoc_mode;
			if (!isset($non_assoc_mode) && $is_enduser) {
				$non_assoc_mode = self::STRING_NONASSOC_MODE_COMMA_LIST;
			}
			
			//non-associative (with mode)
			if (isset($non_assoc_mode) && !empty($strings)) {
				$last_string = array_pop($strings);
				if (empty($strings)) {
					return $last_string;
				}
				$list_string = implode(', ', $strings);
				if ($non_assoc_mode === self::STRING_NONASSOC_MODE_COMMA_LIST_AND) {
					/**
					 * @description Usage of the "and" conjunction in a list, like so: "w, x, y and z".
					 * @placeholder list The comma separated list of elements.
					 * @placeholder last The last element of the list.
					 * @example "foo", "bar" and "zen"
					 */
					return self::localize(
						"{{list}} and {{last}}",
						self::class, $text_options, ['parameters' => ['list' => $list_string, 'last' => $last_string]]
					);
				} elseif ($non_assoc_mode === self::STRING_NONASSOC_MODE_COMMA_LIST_OR) {
					/**
					 * @description Usage of the "or" conjunction in a list, like so: "w, x, y or z".
					 * @placeholder list The comma separated list of elements.
					 * @placeholder last The last element of the list.
					 * @example "foo", "bar" or "zen"
					 */
					return self::localize(
						"{{list}} or {{last}}",
						self::class, $text_options, ['parameters' => ['list' => $list_string, 'last' => $last_string]]
					);
				} elseif ($non_assoc_mode === self::STRING_NONASSOC_MODE_COMMA_LIST_NOR) {
					/**
					 * @description Usage of the "nor" conjunction in a list, like so: "w, x, y nor z".
					 * @placeholder list The comma separated list of elements.
					 * @placeholder last The last element of the list.
					 * @example "foo", "bar" nor "zen"
					 */
					return self::localize(
						"{{list}} nor {{last}}",
						self::class, $text_options, ['parameters' => ['list' => $list_string, 'last' => $last_string]]
					);
				}
				return "{$list_string}, {$last_string}";
			}
			
			//non-associative
			$string = implode(', ', $strings);
			if (!$is_enduser) {
				$string = "[{$string}]";
			}
			return $prepend_type ? "(array){$string}" : $string;
		}
		
		//finalize
		if ($options->no_throw) {
			return null;
		}
		throw new Exceptions\Stringify\UnsupportedValueType([$value]);
	}
	
	/**
	 * Commify a given set of strings.
	 * 
	 * The process of commification of a given set of strings consists into joining them into a single string, 
	 * as a comma separated list of strings.
	 * 
	 * @param string[] $strings
	 * <p>The strings to commify.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param string|null $conjunction [default = null]
	 * <p>The conjunction to commify with.<br>
	 * If set, then it must be one of the following values (case-insensitive): 
	 * <samp>and</samp>, <samp>or</samp> or <samp>nor</samp>.</p>
	 * @param bool $quote [default = false]
	 * <p>Add quotation marks to each one of the given strings.</p>
	 * @return string
	 * <p>The commified string from the given ones.</p>
	 */
	final public static function commify(
		array $strings, $text_options = null, ?string $conjunction = null, bool $quote = false
	): string
	{
		//initialize
		$text_options = TextOptions::coerce($text_options);
		$strings = array_values(array_map('strval', $strings));
		if (isset($conjunction)) {
			$conjunction = strtolower($conjunction);
		}
		
		//guard
		Call::guardParameter(
			'conjunction', $conjunction, !isset($conjunction) || in_array($conjunction, ['and', 'or', 'nor'], true), [
				'hint_message' => "Only the following values are allowed (case-insensitive): " . 
					"\"and\", \"or\" and \"nor\"."
			]
		);
		
		//string non-associative mode
		$string_non_assoc_mode = self::STRING_NONASSOC_MODE_COMMA_LIST;
		if (isset($conjunction)) {
			switch ($conjunction) {
				case 'and':
					$string_non_assoc_mode = self::STRING_NONASSOC_MODE_COMMA_LIST_AND;
					break;
				case 'or':
					$string_non_assoc_mode = self::STRING_NONASSOC_MODE_COMMA_LIST_OR;
					break;
				case 'nor':
					$string_non_assoc_mode = self::STRING_NONASSOC_MODE_COMMA_LIST_NOR;
					break;
			}
		}
		
		//return
		return self::stringify($strings, $text_options, [
			'non_assoc_mode' => $string_non_assoc_mode,
			'quote_strings' => $quote
		]);
	}
	
	/**
	 * Slugify a given string.
	 * 
	 * The process of slugification of a given string consists in converting all of its characters into 
	 * the closest ones in the ASCII alphanumeric range (<samp>0-9</samp>, <samp>a-z</samp> and <samp>A-Z</samp>), 
	 * discarding all special characters and replacing word separator characters, such as spaces, underscores, commas, 
	 * periods and others of a similar type, by a given delimiter.<br>
	 * <br>
	 * The returning string is also trimmed and converted to lowercase by omission.
	 * 
	 * @param string $string
	 * <p>The string to slugify.</p>
	 * @param bool $keep_case [default = false]
	 * <p>Keep the original string case.</p>
	 * @param string $delimiter [default = '-']
	 * <p>The delimiter character to use between words.</p>
	 * @return string
	 * <p>The slugified string from the given one.</p>
	 */
	final public static function slugify(string $string, bool $keep_case = false, string $delimiter = '-'): string
	{
		//guard
		Call::guardParameter('delimiter', $delimiter, strlen($delimiter) === 1, [
			'hint_message' => "Only a single ASCII character is allowed."
		]);
		
		//slugify
		$string = preg_replace('/[^\pL\d]+/iu', $delimiter, $string);
		$string = iconv(Locale::getEncoding(), 'ASCII//TRANSLIT', $string);
		$string = trim(preg_replace('/[^a-z\d' . preg_quote($delimiter, '/') . ']+/i', '', $string), $delimiter);
		return $keep_case ? $string : strtolower($string);
	}
	
	/**
	 * Unslugify a given string.
	 * 
	 * The process of unslugification of a given string consists in a best attempt to collapse all delimiter characters 
	 * into spaces, and keep all the non-delimiter characters intact (ASCII alphanumeric characters), 
	 * which results into human-readable words from the original slugified string, 
	 * although they might not fully correspond to the original string before it was slugified.
	 * 
	 * @param string $string
	 * <p>The string to unslugify.</p>
	 * @param int $flags [default = 0x00]
	 * <p>The flags to use, which can be any combination of the following:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNSLUG_CAPITALIZE_FIRST</code> : 
	 * Capitalize the first word of the unslugified string.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNSLUG_CAPITALIZE_ALL</code> : 
	 * Capitalize all the words of the unslugified string.</p>
	 * @return string
	 * <p>The unslugified string from the given one.</p>
	 */
	final public static function unslugify(string $string, int $flags = 0x00): string
	{
		$string = trim(preg_replace('/[^a-z\d]+/i', ' ', $string));
		if ($flags & self::UNSLUG_CAPITALIZE_ALL) {
			$string = ucwords($string);
		} elseif ($flags & self::UNSLUG_CAPITALIZE_FIRST) {
			$string = ucfirst($string);
		}
		return $string;
	}
	
	/**
	 * Bulletify a given string.
	 * 
	 * The process of bulletification of a given string consists in prepending a bullet character to it, 
	 * turning the string into a bullet point, in a way to be safely localized for different languages.
	 * 
	 * @param string $string
	 * <p>The string to bulletify.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Bulletify|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The bulletified string from the given one.</p>
	 */
	final public static function bulletify(string $string, $text_options = null, $options = null): string
	{
		//initialize
		$options = Options\Bulletify::coerce($options);
		
		//indentate multiline
		if (preg_match('/^(?P<first>[^\n]*)\n(?P<second>.*)$/sm', $string, $matches)) {
			$string = "{$matches['first']}\n" . self::indentate($matches['second'], 1, '   ');
		}
		
		//bulletify
		/**
		 * @description Text bulletification.
		 * @placeholder bullet The bullet character.
		 * @placeholder text The text to prepend the bullet to.
		 * @example  &#8226; this is a bullet point;
		 */
		return self::localize(
			" {{bullet}} {{text}}",
			self::class, $text_options, ['parameters' => ['bullet' => $options->bullet, 'text' => $string]]
		);
	}
	
	/**
	 * Bulletify a given set of strings.
	 * 
	 * The process of bulletification of a given set of strings consists in prepending a bullet character 
	 * to each string, turning the strings into bullet points, in a way to be safely localized for different languages.
	 * 
	 * @param string[] $strings
	 * <p>The strings to bulletify.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Mbulletify|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string[]|string
	 * <p>The bulletified strings from the given ones.<br>
	 * The original index association and sort of the strings array is preserved.<br>
	 * If <var>$options->merge</var> is set to boolean <code>true</code>, 
	 * then a single merged string with all the given strings is returned instead, with each string in a new line.</p>
	 */
	final public static function mbulletify(array $strings, $text_options = null, $options = null)
	{
		//initialize
		$options = Options\Mbulletify::coerce($options);
		
		//bulletify
		foreach ($strings as &$string) {
			$string = self::bulletify($string, $text_options, ['bullet' => $options->bullet]);
		}
		unset($string);
		
		//punctuate
		if ($options->punctuate) {
			$last_key = Data::klast($strings, true);
			foreach ($strings as $key => &$string) {
				if ($key === $last_key) {
					/**
					 * @description Bullet point text punctuation with period.
					 * @placeholder text The text to punctuate.
					 * @example  &#8226; this is the last bullet point.
					 */
					$string = self::localize(
						"{{text}}.",
						self::class, $text_options, ['parameters' => ['text' => $string]]
					);
				} else {
					/**
					 * @description Bullet point text punctuation with semicolon.
					 * @placeholder text The text to punctuate.
					 * @example  &#8226; this is a bullet point;
					 */
					$string = self::localize(
						"{{text}};",
						self::class, $text_options, ['parameters' => ['text' => $string]]
					);
				}
			}
			unset($string);
		}
		
		//newlines
		if ($options->append_newline || $options->multiline_newline_append) {
			foreach ($strings as &$string) {
				if ($options->append_newline) {
					$string .= "\n";
				}
				if ($options->multiline_newline_append && self::multiline($string)) {
					$string .= "\n";
				}
			}
			unset($string);
		}
		
		//return
		return $options->merge ? implode("\n", $strings) : $strings;
	}
	
	/**
	 * Check if a given string is an identifier.
	 * 
	 * A given string is only considered to be an identifier as a word which starts with 
	 * an ASCII letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and is exclusively composed of ASCII letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param bool $extended [default = false]
	 * <p>Check as an extended identifier, 
	 * in which dots may be used as delimiters between words to represent pointers.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is an identifier.</p>
	 */
	final public static function isIdentifier(string $string, bool $extended = false): bool
	{
		return preg_match($extended ? '/^[a-z_]\w*(?:\.[a-z_]\w*)*$/i' : '/^[a-z_]\w*$/i', $string);
	}
	
	/**
	 * Check if a given string matches a given wildcard.
	 * 
	 * In a given wildcard, the <samp>*</samp> character matches any number and type of characters, 
	 * including no characters at all, and is also the only wildcard character recognized.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string $wildcard
	 * <p>The wildcard to match against.</p>
	 * @param bool $insensitive [default = false]
	 * <p>Perform a case-insensitive matching.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string matches the given wildcard.</p>
	 */
	final public static function wildcardMatch(string $string, string $wildcard, bool $insensitive = false): bool
	{
		$pattern = '/^' . 
			implode('.*', array_map(function ($s) { return preg_quote($s, '/'); }, explode('*', $wildcard))) . 
			'$/';
		if ($insensitive) {
			$pattern .= 'i';
		}
		return preg_match($pattern, $string);
	}
	
	/**
	 * Check if a given string matches any given wildcards.
	 * 
	 * In any given wildcard, the <samp>*</samp> character matches any number and type of characters, 
	 * including no characters at all, and is also the only wildcard character recognized.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string[] $wildcards
	 * <p>The wildcards to match against.</p>
	 * @param bool $insensitive [default = false]
	 * <p>Perform a case-insensitive matching.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string matches any of the given wildcards.</p>
	 */
	final public static function anyWildcardsMatch(string $string, array $wildcards, bool $insensitive = false): bool
	{
		foreach ($wildcards as $wildcard) {
			if (self::wildcardMatch($string, $wildcard, $insensitive)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Fill a given string with a given set of parameters.
	 * 
	 * The process of filling a given string consists in replacing its placeholders by the given parameters, 
	 * with each parameter being stringified.<br>
	 * <br>
	 * Placeholders must be set in the string as <samp>{{placeholder}}</samp>, and they must be exclusively composed of 
	 * identifiers, which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties 
	 * or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <samp>{{object.property}}</samp>, with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param string $string
	 * <p>The string to fill.</p>
	 * @param array $parameters
	 * <p>The parameters to fill the respective placeholders with, as a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Fill|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\InvalidPlaceholder
	 * @return string
	 * <p>The given string filled with the given set of parameters.</p>
	 */
	final public static function fill(string $string, array $parameters, $text_options = null, $options = null): string
	{
		//initialize
		$options = Options\Fill::coerce($options);
		
		//tokenize
		$f_string = '';
		foreach (preg_split('/\{{2}([^{}]*)\}{2}/Us', $string, flags: PREG_SPLIT_DELIM_CAPTURE) as $i => $token) {
			//string
			if ($i % 2 === 0) {
				$f_string .= $token;
				continue;
			}
			
			//token
			$token = trim($token);
			if (!self::isPlaceholder($token)) {
				throw new Exceptions\InvalidPlaceholder([$token, 'string' => $string]);
			}
			
			//pointer
			$pointers = [$parameters, $options->object];
			$pointer = $pointers[0];
			foreach (explode('.', $token) as $j => $identifier) {
				//method
				if ($identifier[-1] === ')') {
					//initialize
					$identifier = substr($identifier, 0, -2);
					if ($j === 0) {
						foreach ($pointers as $p) {
							if (is_object($p) && method_exists($p, $identifier)) {
								$pointer = $p;
								break;
							}
						}
					}
					
					//check
					if (!is_object($pointer)) {
						Call::haltParameter('string', $string, [
							'error_message' => "Invalid method identifier {{identifier}} " . 
								"in placeholder {{placeholder}} for {{pointer}}.",
							'hint_message' => "The corresponding pointer must be an object.",
							'parameters' => [
								'identifier' => "{$identifier}()", 'placeholder' => $token, 'pointer' => $pointer
							]
						]);
					} elseif (!method_exists($pointer, $identifier)) {
						Call::haltParameter('string', $string, [
							'error_message' => "Method identifier {{identifier}} in placeholder {{placeholder}} " . 
								"not found in {{pointer}}.",
							'parameters' => [
								'identifier' => "{$identifier}()", 'placeholder' => $token, 'pointer' => $pointer
							]
						]);
					}
					
					//finalize
					$pointer = $pointer->$identifier();
					
				//property or key
				} else {
					//initialize
					if ($j === 0) {
						foreach ($pointers as $p) {
							if (
								(is_object($p) && property_exists($p, $identifier)) || 
								(is_array($p) && array_key_exists($identifier, $p))
							) {
								$pointer = $p;
								break;
							}
						}
					}
					
					//property
					if (is_object($pointer)) {
						if (!property_exists($pointer, $identifier)) {
							Call::haltParameter('string', $string, [
								'error_message' => "Property identifier {{identifier}} " . 
									"in placeholder {{placeholder}} not found in {{pointer}}.",
								'parameters' => [
									'identifier' => $identifier, 'placeholder' => $token, 'pointer' => $pointer
								]
							]);
						}
						$pointer = $pointer->$identifier;
						
					//key
					} elseif (is_array($pointer)) {
						if (!array_key_exists($identifier, $pointer)) {
							Call::haltParameter('string', $string, [
								'error_message' => "Key identifier {{identifier}} in placeholder {{placeholder}} " . 
									"not found in {{pointer}}.",
								'parameters' => [
									'identifier' => $identifier, 'placeholder' => $token, 'pointer' => $pointer
								]
							]);
						}
						$pointer = $pointer[$identifier];
						
					//halt
					} else {
						Call::haltParameter('string', $string, [
							'error_message' => "Invalid identifier {{identifier}} in placeholder {{placeholder}} " . 
								"for {{pointer}}.",
							'hint_message' => "The corresponding pointer must be an object or array.",
							'parameters' => [
								'identifier' => $identifier, 'placeholder' => $token, 'pointer' => $pointer
							]
						]);
					}
				}
			}
			
			//stringify
			$pointer_string = null;
			if ($options->stringifier !== null) {
				$pointer_string = ($options->stringifier)($token, $pointer);
			}
			if ($pointer_string === null) {
				$pointer_string = self::stringify($pointer, $text_options, $options->string_options) ?? '';
			}
			
			//finalize
			$f_string .= $pointer_string;
			unset($pointer);
		}
		return $f_string;
	}
	
	/**
	 * Fill a given plural string with a given set of parameters.
	 * 
	 * The process of filling a given string consists in replacing its placeholders by the given parameters, 
	 * with each parameter being stringified.<br>
	 * <br>
	 * Placeholders must be set in the string as <samp>{{placeholder}}</samp>, and they must be exclusively composed of 
	 * identifiers, which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties 
	 * or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <samp>{{object.property}}</samp>, with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param string $string1
	 * <p>The string singular form to fill.</p>
	 * @param string $string2
	 * <p>The string plural form to fill.</p>
	 * @param float $number
	 * <p>The number to use.</p>
	 * @param string|null $number_placeholder [default = null]
	 * <p>The number placeholder to fill with.</p>
	 * @param array $parameters [default = []]
	 * <p>The parameters to fill the respective placeholders with, as a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Pfill|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The given plural string filled with the given set of parameters.</p>
	 */
	final public static function pfill(
		string $string1, string $string2, float $number, ?string $number_placeholder = null, array $parameters = [],
		$text_options = null, $options = null
	): string
	{
		$options = Options\Pfill::coerce($options);
		$string = abs($number) === 1.0 ? $string1 : $string2;
		if ($number === floor($number)) {
			$number = (int)$number;
		}
		if (isset($number_placeholder)) {
			$parameters[$number_placeholder] = $number;
		}
		return self::fill($string, $parameters, $text_options, $options);
	}
	
	/**
	 * Check if a given string is a placeholder.
	 * 
	 * A given string is only considered to be a placeholder if its is exclusively composed of identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * It may also have pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>object.property</samp>, 
	 * with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>object.method()</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is a placeholder.</p>
	 */
	final public static function isPlaceholder(string $string): bool
	{
		return preg_match('/^' . self::PLACEHOLDER_PATTERN . '$/', $string);
	}
	
	/**
	 * Check if a given string has a given placeholder.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string $placeholder
	 * <p>The placeholder to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string has the given placeholder.</p>
	 */
	final public static function hasPlaceholder(string $string, string $placeholder): bool
	{
		Call::guardParameter('placeholder', $placeholder, self::isPlaceholder($placeholder));
		return strpos($string, "{{{$placeholder}}}") !== false;
	}
	
	/**
	 * Check if a given string has placeholders.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string has placeholders.</p>
	 */
	final public static function hasPlaceholders(string $string): bool
	{
		return preg_match('/\{\{' . self::PLACEHOLDER_PATTERN . '\}\}/', $string);
	}
	
	/**
	 * Check if a given string has all given placeholders.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string[] $placeholders
	 * <p>The placeholders to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string has all the given placeholders.</p>
	 */
	final public static function hasAllPlaceholders(string $string, array $placeholders): bool
	{
		foreach ($placeholders as $placeholder) {
			if (!self::hasPlaceholder($string, $placeholder)) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Check if a given string has any given placeholders.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string[] $placeholders
	 * <p>The placeholders to check for.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string has any of the given placeholders.</p>
	 */
	final public static function hasAnyPlaceholders(string $string, array $placeholders): bool
	{
		foreach ($placeholders as $placeholder) {
			if (self::hasPlaceholder($string, $placeholder)) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get placeholders from a given string.
	 * 
	 * Placeholders must be present in the given string as <samp>{{placeholder}}</samp>, 
	 * and they must be exclusively composed of identifiers, which are defined as words which must start with 
	 * a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), and may only contain 
	 * letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values, 
	 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.
	 * 
	 * @param string $string
	 * <p>The string to get from.</p>
	 * @param bool $top_only [default = false]
	 * <p>Return only the top level identifiers.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\InvalidPlaceholder
	 * @return string[]
	 * <p>The placeholders from the given string.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, then any invalid placeholders found are ignored.</p>
	 */
	final public static function placeholders(string $string, bool $top_only = false, bool $no_throw = false): array
	{
		$placeholders = [];
		if (preg_match_all('/\{{2}(?P<placeholders>.*)\}{2}/Us', $string, $matches)) {
			foreach ($matches['placeholders'] as $placeholder) {
				$placeholder = trim($placeholder);
				if (!self::isPlaceholder($placeholder)) {
					if ($no_throw) {
						continue;
					}
					throw new Exceptions\InvalidPlaceholder([$placeholder, 'string' => $string]);
				}
				$placeholders[] = $top_only ? strtok($placeholder, '.') : $placeholder;
			}
			$placeholders = array_values(array_unique($placeholders, SORT_STRING));
		}
		return $placeholders;
	}
	
	/**
	 * Extract parameters from a given string using a given mask.
	 * 
	 * The extraction of parameters from a given string uses a given mask composed of placeholders.<br>
	 * These placeholders are used as the matching wildcards and the corresponding parameter keys to extract the 
	 * corresponding values from the given string.<br>
	 * <br>
	 * Example:<br>
	 * <var>$string</var> = <code>'/home/foo/bar'</code><br>
	 * <var>$mask</var> = <code>'/home/{{user}}/{{path}}'</code><br>
	 * returns:<br>
	 * <code>['user' => 'foo', 'path' => 'bar']</code><br>
	 * <br>
	 * Placeholders must be set in the mask as <samp>{{placeholder}}</samp>, and they must be exclusively composed of 
	 * identifiers, which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
	 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
	 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific associative array keys resulting from the extracted parameters, 
	 * by using a dot between identifiers, such as <samp>{{placeholder.key}}</samp>, 
	 * with no limit on the number of pointers chained.
	 * 
	 * @param string $string
	 * <p>The string to extract from.</p>
	 * @param string $mask
	 * <p>The mask to use.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Extract|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\Extract\MatchFailed
	 * @return array|null
	 * <p>The extracted parameters from the given string using the given mask, 
	 * as a set of <samp>key => value</samp> pairs.<br>
	 * If <var>$options->no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if they could not be extracted.</p>
	 */
	final public static function extract(string $string, string $mask, $options = null): ?array
	{
		//initialize
		$options = Options\Extract::coerce($options);
		$subpatterns = $options->patterns;
		$pattern_modifiers = $options->pattern_modifiers;
		$pattern_delimiter = $options->pattern_delimiter;
		
		//pattern
		$map = [];
		$pattern = '';
		foreach (preg_split('/\{{2}(.*)\}{2}/Us', $mask, flags: PREG_SPLIT_DELIM_CAPTURE) as $i => $token) {
			if ($i % 2 === 0) {
				$pattern .= preg_quote($token, $pattern_delimiter);
			} else {
				//guard
				$token = trim($token);
				Call::guardParameter('mask', $mask, self::isPlaceholder($token), [
					'error_message' => "Invalid placeholder {{placeholder}}.",
					'parameters' => ['placeholder' => $token]
				]);
				
				//subpattern
				$subpattern = $subpatterns[$token] ?? '.*';
				Call::guardParameter(
					'options', $options->getAll(),
					preg_match("{$pattern_delimiter}{$subpattern}{$pattern_delimiter}", null) !== false, [
						'error_message' => "Invalid pattern {{pattern}} for placeholder {{placeholder}}.",
						'parameters' => ['placeholder' => $token, 'pattern' => $subpattern]
					]
				);
				
				//map
				$subpattern_name = "__{$i}";
				$map[$subpattern_name] = $token;
				$pattern .= "(?P<{$subpattern_name}>{$subpattern})";
			}
		}
		$pattern = "{$pattern_delimiter}^{$pattern}\${$pattern_delimiter}{$pattern_modifiers}";
		
		//match
		if (!preg_match($pattern, $string, $matches)) {
			if ($options->no_throw) {
				return null;
			}
			throw new Exceptions\Extract\MatchFailed([$string, $mask]);
		}
		
		//parameters
		$parameters = [];
		foreach ($map as $subpattern_name => $placeholder) {
			$parameters[$placeholder] = $matches[$subpattern_name] ?? null;
		}
		$parameters = Data::expand($parameters);
		
		//return
		return $parameters;
	}
	
	/**
	 * Parse data from a given string.
	 * 
	 * The returning data is parsed from the given string by matching specific patterns with specific fields.<br>
	 * <br>
	 * Thus, instead of using a raw <code>preg_match</code> call with a very lengthy regular expression and having to 
	 * sort out between the matches output by their groups, this function allows smaller patterns to be easily 
	 * set up for each field, and get an associative return with the parsed values set with those fields.
	 * 
	 * @see https://php.net/manual/en/function.preg-match.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.syntax.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.modifiers.php
	 * @param string $string
	 * <p>The string to parse from.</p>
	 * @param string[] $fields_patterns
	 * <p>The fields regular expression patterns to parse with, as a set of <samp>field => pattern</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Parse|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\ParseFailed
	 * @return array|null
	 * <p>The parsed data from the given string, as a set of <samp>field => value</samp> pairs.<br>
	 * If <var>$options->no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if no data could be parsed.</p>
	 */
	final public static function parse(string $string, array $fields_patterns, $options = null): ?array
	{
		//initialize
		$options = Options\Mparse::coerce(Options\Parse::coerce($options), true);
		$no_throw = $options->no_throw;
		$options->no_throw = true;
		$options->keep_nulls = true;
		
		//parse
		$data = self::mparse([$string], $fields_patterns, $options)[0];
		if (!isset($data) && !$no_throw) {
			throw new Exceptions\ParseFailed([$string, $fields_patterns]);
		}
		return $data;
	}
	
	/**
	 * Parse data from multiple given strings.
	 * 
	 * The returning data is parsed from the given strings by matching specific patterns with specific fields.<br>
	 * <br>
	 * Thus, instead of using a raw <code>preg_match</code> with a very lengthy regular expression and having to 
	 * sort out between the matches output by their groups, this function allows smaller patterns to be easily 
	 * set up for each field, and get an associative return per string with the parsed values set with those fields.
	 * 
	 * @see https://php.net/manual/en/function.preg-match.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.syntax.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.modifiers.php
	 * @param string[] $strings
	 * <p>The strings to parse from.</p>
	 * @param string[] $fields_patterns
	 * <p>The fields regular expression patterns to parse with, as a set of <samp>field => pattern</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Mparse|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\ParseFailed
	 * @return array
	 * <p>The parsed data from the given strings, as a set of <samp>field => value</samp> pairs per string, 
	 * or <code>null</code> per string if <var>$options->keep_nulls</var> is set to boolean <code>true</code> 
	 * and no data could be parsed from it.<br>
	 * The original index association and sort of the strings array is preserved.</p>
	 */
	final public static function mparse(array $strings, array $fields_patterns, $options = null): array
	{
		//initialize
		$options = Options\Mparse::coerce($options);
		$pattern_delimiter = $options->pattern_delimiter;
		$delimiter_pattern = $options->delimiter_pattern;
		$pattern_modifiers = $options->pattern_modifiers;
		$keep_nulls = $options->keep_nulls;
		$no_throw = $options->no_throw;
		$strings = Data::coerce($strings, function (&$key, &$value): bool {
			return Type::evaluateString($value);
		});
		
		//fields patterns
		if (empty($fields_patterns)) {
			return $keep_nulls ? array_fill_keys(array_keys($strings), null) : [];
		} else {
			foreach ($fields_patterns as $field => $pattern) {
				$field_pattern = $pattern_delimiter . $pattern . $pattern_delimiter;
				Call::guardParameter(
					'fields_patterns', $fields_patterns,
					is_string($pattern) && preg_match($field_pattern, null) !== false, [
						'error_message' => "Invalid pattern {{pattern}} for field {{field}}.",
						'hint_message' => "Only a valid regular expression is allowed.",
						'parameters' => ['pattern' => $pattern, 'field' => $field]
					]
				);
			}
		}
		
		//guard delimiter pattern
		Call::guardParameter(
			'options', $options->getAll(),
			preg_match($pattern_delimiter . $delimiter_pattern . $pattern_delimiter, null) !== false, [
				'error_message' => "Invalid delimiter pattern {{pattern}}.",
				'hint_message' => "Only a valid regular expression is allowed.",
				'parameters' => ['pattern' => $delimiter_pattern]
			]
		);
		
		//prepare
		$group = 1;
		$groups_fields = [];
		foreach ($fields_patterns as $field => $pattern) {
			$groups_fields[$group] = $field;
			$group += preg_match_all('/(?<!\\\\)\((?!\?[!:=<>])/', $pattern) + 1;
		}
		unset($group);
		$pattern = "{$pattern_delimiter}^(" . 
			implode("){$delimiter_pattern}(", $fields_patterns) . 
			")\${$pattern_delimiter}{$pattern_modifiers}";
		
		//parse
		$strings_fields_values = [];
		foreach ($strings as $key => $string) {
			if (preg_match($pattern, $string, $matches)) {
				foreach ($groups_fields as $group => $field) {
					$strings_fields_values[$key][$field] = $matches[$group] ?? null;
				}
			} elseif ($keep_nulls) {
				$strings_fields_values[$key] = null;
			} elseif (!$no_throw) {
				throw new Exceptions\ParseFailed([$string, $fields_patterns, 'key' => $key]);
			}
		}
		
		//return
		return $strings_fields_values;
	}
	
	/**
	 * Convert the first letter of a given string to lowercase.
	 * 
	 * @see https://php.net/manual/en/function.lcfirst.php
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $unicode [default = false]
	 * <p>Convert as a Unicode string.</p>
	 * @return string
	 * <p>The given string with the first letter converted to lowercase.</p>
	 */
	final public static function lcfirst(string $string, bool $unicode = false): string
	{
		if ($unicode) {
			$encoding = Locale::getEncoding();
			return mb_strtolower(mb_substr($string, 0, 1, $encoding), $encoding) . 
				mb_substr($string, 1, null, $encoding);
		}
		return lcfirst($string);
	}
	
	/**
	 * Convert the first letter of a given string to uppercase.
	 * 
	 * @see https://php.net/manual/en/function.ucfirst.php
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $unicode [default = false]
	 * <p>Convert as a Unicode string.</p>
	 * @return string
	 * <p>The given string with the first letter converted to uppercase.</p>
	 */
	final public static function ucfirst(string $string, bool $unicode = false): string
	{
		if ($unicode) {
			$encoding = Locale::getEncoding();
			return mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding) . 
				mb_substr($string, 1, null, $encoding);
		}
		return ucfirst($string);
	}
	
	/**
	 * Calculate the length of a given string.
	 * 
	 * @param string $string
	 * <p>The string to calculate from.</p>
	 * @param bool $unicode [default = false]
	 * <p>Calculate as a Unicode string.</p>
	 * @return int
	 * <p>The length of the given string.</p>
	 */
	final public static function length(string $string, bool $unicode = false): int
	{
		return $unicode ? mb_strlen($string, Locale::getEncoding()) : strlen($string);
	}
	
	/**
	 * Convert a given string to uppercase.
	 * 
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $unicode [default = false]
	 * <p>Convert as a Unicode string.</p>
	 * @return string
	 * <p>The given string converted to uppercase.</p>
	 */
	final public static function upper(string $string, bool $unicode = false): string
	{
		return $unicode ? mb_strtoupper($string, Locale::getEncoding()) : strtoupper($string);
	}
	
	/**
	 * Convert a given string to lowercase.
	 * 
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $unicode [default = false]
	 * <p>Convert as a Unicode string.</p>
	 * @return string
	 * <p>The given string converted to lowercase.</p>
	 */
	final public static function lower(string $string, bool $unicode = false): string
	{
		return $unicode ? mb_strtolower($string, Locale::getEncoding()) : strtolower($string);
	}
	
	/**
	 * Get sub-string from a given string from a given starting index.
	 * 
	 * @see https://php.net/manual/en/function.substr.php
	 * @param string $string
	 * <p>The string to get from.</p>
	 * @param int $start
	 * <p>The starting index to get from, with <code>0</code> corresponding to the first character.<br>
	 * If negative, then it is interpreted as starting at the end of the given string, 
	 * with the last character corresponding to <code>-1</code>.</p>
	 * @param int|null $length [default = null]
	 * <p>The maximum length of the returning sub-string.<br>
	 * If negative, then it is interpreted as the number of characters to remove from the end of the given string.<br>
	 * If not set, then it is interpreted as being the exact length of the given string.</p>
	 * @param bool $unicode [default = false]
	 * <p>Get as a Unicode string.</p>
	 * @return string
	 * <p>The sub-string from the given string from the given starting index.</p>
	 */
	final public static function sub(string $string, int $start, ?int $length = null, bool $unicode = false): string
	{
		return $unicode
			? mb_substr($string, $start, $length, Locale::getEncoding())
			: (isset($length) ? substr($string, $start, $length) : substr($string, $start));
	}
	
	/**
	 * Capitalize a given string.
	 * 
	 * The process of capitalization of a given string consists in converting the first character 
	 * from its first word to uppercase, but only if it is safe to do so.
	 * 
	 * @param string $string
	 * <p>The string to capitalize.</p>
	 * @param bool $unicode [default = false]
	 * <p>Capitalize as a Unicode string.</p>
	 * @return string
	 * <p>The given string capitalized.</p>
	 */
	final public static function capitalize(string $string, bool $unicode = false): string
	{
		$pattern = $unicode
			? '/^(?P<start>[^\pL]*)(?P<first_word>\pL[\pL\-]*)(?P<end>.*)$/usm'
			: '/^(?P<start>[^a-z]*)(?P<first_word>[a-z][a-z\-]*)(?P<end>.*)$/ism';
		if (
			preg_match($pattern, $string, $matches) && 
			self::lower($matches['first_word'], $unicode) === $matches['first_word']
		) {
			return $matches['start'] . self::ucfirst($matches['first_word'], $unicode) . $matches['end'];
		}
		return $string;
	}
	
	/**
	 * Uncapitalize a given string.
	 * 
	 * The process of uncapitalization of a given string consists in converting the first character 
	 * from its first word to lowercase, but only if it is safe to do so.
	 * 
	 * @param string $string
	 * <p>The string to uncapitalize.</p>
	 * @param bool $unicode [default = false]
	 * <p>Uncapitalize as a Unicode string.</p>
	 * @return string
	 * <p>The given string uncapitalized.</p>
	 */
	final public static function uncapitalize(string $string, bool $unicode = false): string
	{
		$pattern = $unicode
			? '/^(?P<start>[^\pL]*)(?P<first_word>\pL[\pL\-]*)(?P<end>.*)$/usm'
			: '/^(?P<start>[^a-z]*)(?P<first_word>[a-z][a-z\-]*)(?P<end>.*)$/ism';
		if (
			preg_match($pattern, $string, $matches) && 
			self::ucfirst(self::lower($matches['first_word'], $unicode), $unicode) === $matches['first_word']
		) {
			return $matches['start'] . self::lcfirst($matches['first_word'], $unicode) . $matches['end'];
		}
		return $string;
	}
	
	/**
	 * Calculate the distance between two given strings.
	 * 
	 * The returning distance between the two given strings is calculated by using the Levenshtein distance algorithm, 
	 * which defines the distance as the minimum number of inserts, deletes and substitutions which need to take place 
	 * to convert one string into another.<br>
	 * <br>
	 * Alternatively, its Damerau variation (Damerau-Levenshtein) can be used to also consider transpositions 
	 * of 2 adjacent characters to result into a distance of 1 (1 transposition) instead of 2 (2 substitutions).
	 * 
	 * @see https://en.wikipedia.org/wiki/Levenshtein_distance
	 * @see https://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	 * @param string $string1
	 * <p>The first string, to calculate with.</p>
	 * @param string $string2
	 * <p>The second string, to calculate with.</p>
	 * @param bool $damerau [default = false]
	 * <p>Use the Damerau variation of the algorithm (Damerau-Levenshtein).</p>
	 * @param bool $insensitive [default = false]
	 * <p>Perform a case-insensitive calculation.</p>
	 * @param bool $unicode [default = false]
	 * <p>Calculate the distance as Unicode.</p>
	 * @return int
	 * <p>The distance between the two given strings.</p>
	 */
	final public static function distance(
		string $string1, string $string2, bool $damerau = false, bool $insensitive = false, bool $unicode = false
	): int
	{
		//prepare
		if ($insensitive) {
			$string1 = self::lower($string1, $unicode);
			$string2 = self::lower($string2, $unicode);
		}
		$length1 = self::length($string1, $unicode);
		$length2 = self::length($string2, $unicode);
		
		//optimization
		if ($string1 === $string2) {
			return 0;
		} elseif ($length1 === 0) {
			return $length2;
		} elseif ($length2 === 0) {
			return $length1;
		}
		
		//matrix
		$matrix = array_fill_keys(range(0, $length1), array_fill_keys(range(0, $length2), 0));
		for ($i1 = 1; $i1 <= $length1; $i1++) {
			$matrix[$i1][0] = $i1;
		}
		for ($i2 = 1; $i2 <= $length2; $i2++) {
			$matrix[0][$i2] = $i2;
		}
		
		//characters
		$chars1 = $chars2 = [];
		for ($i1 = 0; $i1 < $length1; $i1++) {
			$chars1[] = $unicode ? self::sub($string1, $i1, 1, true) : $string1[$i1];
		}
		for ($i2 = 0; $i2 < $length2; $i2++) {
			$chars2[] = $unicode ? self::sub($string2, $i2, 1, true) : $string2[$i2];
		}
		
		//calculate
		for ($i2 = 1; $i2 <= $length2; $i2++) {
			for ($i1 = 1; $i1 <= $length1; $i1++) {
				$cost = $chars1[$i1 - 1] === $chars2[$i2 - 1] ? 0 : 1;
				$matrix[$i1][$i2] = min(
					$matrix[$i1 - 1][$i2] + 1, 
					$matrix[$i1][$i2 - 1] + 1,
					$matrix[$i1 - 1][$i2 - 1] + $cost
				);
				if (
					$damerau && $i1 > 1 && $i2 > 1 && 
					$chars1[$i1 - 1] === $chars2[$i2 - 2] && $chars1[$i1 - 2] === $chars2[$i2 - 1]
				) {
					$matrix[$i1][$i2] = min($matrix[$i1][$i2], $matrix[$i1 - 2][$i2 - 2] + $cost);
				}
			}
		}
		
		//return
		return $matrix[$length1][$length2];
	}
	
	/**
	 * Truncate a given string to a given length.
	 * 
	 * @param string $string
	 * <p>The string to truncate.</p>
	 * @param int $length
	 * <p>The length to truncate to.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Truncate|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The given string truncated to the given length.</p>
	 */
	final public static function truncate(string $string, int $length, $options = null): string
	{
		//guard
		Call::guardParameter('length', $length, $length >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		
		//initialize
		$options = Options\Truncate::coerce($options);
		$unicode = $options->unicode;
		$end_string = '';
		
		//check
		if (self::length($string, $unicode) <= $length) {
			return $string;
		}
		
		//ellipsis
		if ($options->ellipsis) {
			$end_string = $options->ellipsis_string ?? ($unicode ? "\u{2026}" : '...');
			$length -= self::length($end_string, $unicode);
			if ($length <= 0) {
				return $end_string;
			}
		}
		
		//sentences
		if ($options->keep_sentences) {
			$sentences_pattern = $unicode ? '/\P{Po}+\p{Po}+/u' : '/[^\.\?\!]+[\.\?\!]+/';
			if (preg_match_all($sentences_pattern, $string, $matches)) {
				$t_length = 0;
				$t_string = '';
				foreach ($matches[0] as $sentence) {
					$sentence_length = self::length($sentence, $unicode);
					if ($t_length + $sentence_length > $length) {
						break;
					}
					$t_string .= $sentence;
					$t_length += $sentence_length;
				}
				$t_string = trim($t_string);
				if ($t_string !== '') {
					return $t_string . $end_string;
				}
				unset($t_string);
			}
		}
		
		//words
		if ($options->keep_words) {
			$t_length = 0;
			$t_string = '';
			$words_pattern = $unicode ? '/([\pL\pN_]+(?:-[\pL\pN_]+)*)/u' : '/(\w+(?:-\w+)*)/';
			foreach (preg_split($words_pattern, $string, flags: PREG_SPLIT_DELIM_CAPTURE) as $part) {
				$part_length = self::length($part, $unicode);
				if ($t_length + $part_length > $length) {
					break;
				}
				$t_string .= $part;
				$t_length += $part_length;
			}
			$t_string = trim($t_string);
			if ($t_string !== '') {
				return $t_string . $end_string;
			}
			unset($t_string);
		}
		
		//return
		return self::sub($string, 0, $length, $unicode) . $end_string;
	}
	
	/**
	 * Indentate a given string.
	 * 
	 * @param string $string
	 * <p>The string to indentate.</p>
	 * @param int $level [default = 1]
	 * <p>The level to indentate with.</p>
	 * @param string $expression [default = "\t"]
	 * <p>The expression to indentate with.</p>
	 * @return string
	 * <p>The given string indentated.</p>
	 */
	final public static function indentate(string $string, int $level = 1, string $expression = "\t"): string
	{
		Call::guardParameter('level', $level, $level >= 0, [
			'hint_message' => "Only a value greater than or equal to 0 is allowed."
		]);
		Call::guardParameter('expression', $expression, $expression !== '', [
			'error_message' => "An empty value is not allowed."
		]);
		return $level > 0 ? preg_replace('/^/m', str_repeat($expression, $level), $string) : $string;
	}
	
	/**
	 * Check if a given string is multiline.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is multiline.</p>
	 */
	final public static function multiline(string $string): bool
	{
		return strpos($string, "\n") !== false;
	}
	
	/**
	 * Check if a given string is in camel case notation.
	 * 
	 * A given string is only considered to be in camel case notation if it starts with a lowercase character 
	 * from <samp>a</samp> to <samp>z</samp> and is only composed of ASCII alphanumeric characters 
	 * (<samp>0-9</samp>, <samp>a-z</samp> and <samp>A-Z</samp>).<br>
	 * <br>
	 * The strings <samp>foo</samp> and <samp>fooBar</samp> are two examples of camel case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in camel case notation.</p>
	 */
	final public static function isCamelCase(string $string): bool
	{
		return preg_match('/^[a-z][A-Za-z\d]*$/', $string);
	}
	
	/**
	 * Check if a given string is in pascal case notation.
	 * 
	 * A given string is only considered to be in pascal case notation if it starts with an uppercase character 
	 * from <samp>A</samp> to <samp>Z</samp> and is only composed of ASCII alphanumeric characters 
	 * (<samp>0-9</samp>, <samp>a-z</samp> and <samp>A-Z</samp>).<br>
	 * <br>
	 * The strings <samp>Foo</samp> and <samp>FooBar</samp> are two examples of pascal case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in pascal case notation.</p>
	 */
	final public static function isPascalCase(string $string): bool
	{
		return preg_match('/^[A-Z][A-Za-z\d]*$/', $string);
	}
	
	/**
	 * Check if a given string is in snake case notation.
	 * 
	 * A given string is only considered to be in snake case notation if it starts with a lowercase character 
	 * from <samp>a</samp> to <samp>z</samp> and is only composed of lowercase ASCII alphanumeric characters 
	 * (<samp>0-9</samp> and <samp>a-z</samp>), with words delimited by 
	 * a single underscore (<samp>_</samp>) between them.<br>
	 * <br>
	 * The strings <samp>foo</samp> and <samp>foo_bar</samp> are two examples of snake case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in snake case notation.</p>
	 */
	final public static function isSnakeCase(string $string): bool
	{
		return preg_match('/^[a-z][a-z\d]*(?:_[a-z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in kebab case notation.
	 * 
	 * A given string is only considered to be in kebab case notation if it starts with a lowercase character 
	 * from <samp>a</samp> to <samp>z</samp> and is only composed of lowercase ASCII alphanumeric characters 
	 * (<samp>0-9</samp> and <samp>a-z</samp>), with words delimited by 
	 * a single hyphen (<samp>-</samp>) between them.<br>
	 * <br>
	 * The strings <samp>foo</samp> and <samp>foo-bar</samp> are two examples of kebab case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in kebab case notation.</p>
	 */
	final public static function isKebabCase(string $string): bool
	{
		return preg_match('/^[a-z][a-z\d]*(?:-[a-z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in macro case notation.
	 * 
	 * A given string is only considered to be in macro case notation if it starts with an uppercase character 
	 * from <samp>A</samp> to <samp>Z</samp> and is only composed of uppercase ASCII alphanumeric characters 
	 * (<samp>0-9</samp> and <samp>A-Z</samp>), with words delimited by 
	 * a single underscore (<samp>_</samp>) between them.<br>
	 * <br>
	 * The strings <samp>FOO</samp> and <samp>FOO_BAR</samp> are two examples of macro case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in macro case notation.</p>
	 */
	final public static function isMacroCase(string $string): bool
	{
		return preg_match('/^[A-Z][A-Z\d]*(?:_[A-Z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in cobol case notation.
	 * 
	 * A given string is only considered to be in cobol case notation if it starts with an uppercase character 
	 * from <samp>A</samp> to <samp>Z</samp> and is only composed of uppercase ASCII alphanumeric characters 
	 * (<samp>0-9</samp> and <samp>A-Z</samp>), with words delimited by 
	 * a single hyphen (<samp>-</samp>) between them.<br>
	 * <br>
	 * The strings <samp>FOO</samp> and <samp>FOO-BAR</samp> are two examples of cobol case notation.
	 * 
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>String to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is in cobol case notation.</p>
	 */
	final public static function isCobolCase(string $string): bool
	{
		return preg_match('/^[A-Z][A-Z\d]*(?:-[A-Z\d]+)*$/', $string);
	}
	
	/**
	 * Get case notation from a given string.
	 * 
	 * The returning case notation from the given string is recognized by checking mostly the case of each character, 
	 * and only strings exclusively composed of ASCII alphanumeric characters 
	 * (<samp>0-9</samp>, <samp>a-z</samp> and <samp>A-Z</samp>), optionally with underscore (<samp>_</samp>) 
	 * or hyphen (<samp>-</samp>) as delimiters, are considered.<br>
	 * <br>
	 * The following are some examples of each notation:<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> and <samp>fooBar</samp> are in camel case notation.<br>
	 * &nbsp; &#8226; &nbsp; <samp>Foo</samp> and <samp>FooBar</samp> are in pascal case notation.<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> and <samp>foo_bar</samp> are in snake case notation.<br>
	 * &nbsp; &#8226; &nbsp; <samp>foo</samp> and <samp>foo-bar</samp> are in kebab case notation.<br>
	 * &nbsp; &#8226; &nbsp; <samp>FOO</samp> and <samp>FOO_BAR</samp> are in macro case notation.<br>
	 * &nbsp; &#8226; &nbsp; <samp>FOO</samp> and <samp>FOO-BAR</samp> are in cobol case notation.<br>
	 * <br>
	 * Some strings may become ambiguous such as <samp>foo</samp> which is simultaneously in snake, 
	 * kebab and camel case notations, therefore the desambiguation is solved by performing the internal checks 
	 * in the following order:<br>
	 * 1 - Snake case notation check;<br>
	 * 2 - Kebab case notation check;<br>
	 * 3 - Macro case notation check;<br>
	 * 4 - Cobol case notation check;<br>
	 * 5 - Camel case notation check;<br>
	 * 6 - Pascal case notation check.<br>
	 * <br>
	 * In other words, <samp>foo</samp> will be recognized to be in snake case notation only, 
	 * since the snake case check is performed before the kebab and camel case ones.
	 * 
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to get from.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The case notation of a given string as:<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_SNAKE</code> for snake case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_KEBAB</code> for kebab case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_MACRO</code> for macro case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_COBOL</code> for cobol case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_CAMEL</code> for camel case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <code>self::CASE_PASCAL</code> for pascal case.<br>
	 * <br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if no notation was recognized.</p>
	 */
	final public static function caseNotation(string $string, bool $no_throw = false): ?string
	{
		if (self::isSnakeCase($string)) {
			return self::CASE_SNAKE;
		} elseif (self::isKebabCase($string)) {
			return self::CASE_KEBAB;
		} elseif (self::isMacroCase($string)) {
			return self::CASE_MACRO;
		} elseif (self::isCobolCase($string)) {
			return self::CASE_COBOL;
		} elseif (self::isCamelCase($string)) {
			return self::CASE_CAMEL;
		} elseif (self::isPascalCase($string)) {
			return self::CASE_PASCAL;
		} elseif ($no_throw) {
			return null;
		}
		throw new Exceptions\UnknownCaseNotation([$string]);
	}
	
	/**
	 * Convert a given string to camel case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in pascal, snake, kebab, macro or cobol case notation.<br>
	 * If given in camel case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to camel case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toCamelCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_CAMEL) {
			return $string;
		} elseif ($notation === self::CASE_PASCAL) {
			return lcfirst($string);
		} elseif (in_array($notation, [self::CASE_SNAKE, self::CASE_KEBAB, self::CASE_MACRO, self::CASE_COBOL], true)) {
			$delimiter = $notation === self::CASE_SNAKE || $notation === self::CASE_MACRO ? '_' : '-';
			$words = explode($delimiter, strtolower($string));
			foreach ($words as $i => &$word) {
				if ($i > 0) {
					$word = ucfirst($word);
				}
			}
			unset($word);
			return implode($words);
		}
		return null;
	}
	
	/**
	 * Convert a given string to pascal case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in camel, snake, kebab, macro or cobol case notation.<br>
	 * If given in pascal case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to pascal case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toPascalCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_PASCAL) {
			return $string;
		} elseif ($notation === self::CASE_CAMEL) {
			return ucfirst($string);
		} elseif (in_array($notation, [self::CASE_SNAKE, self::CASE_KEBAB, self::CASE_MACRO, self::CASE_COBOL], true)) {
			$delimiter = $notation === self::CASE_SNAKE || $notation === self::CASE_MACRO ? '_' : '-';
			return implode(array_map('ucfirst', explode($delimiter, strtolower($string))));
		}
		return null;
	}
	
	/**
	 * Convert a given string to snake case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in camel, pascal, kebab, macro or cobol case notation.<br>
	 * If given in snake case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to snake case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toSnakeCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_SNAKE) {
			return $string;
		} elseif ($notation === self::CASE_MACRO) {
			return strtolower($string);
		} elseif ($notation === self::CASE_KEBAB) {
			return str_replace('-', '_', $string);
		} elseif ($notation === self::CASE_COBOL) {
			return str_replace('-', '_', strtolower($string));
		} elseif (
			($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && 
			preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)
		) {
			return strtolower(implode('_', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to kebab case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in camel, pascal, snake, macro or cobol case notation.<br>
	 * If given in kebab case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to kebab case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toKebabCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_KEBAB) {
			return $string;
		} elseif ($notation === self::CASE_COBOL) {
			return strtolower($string);
		} elseif ($notation === self::CASE_SNAKE) {
			return str_replace('_', '-', $string);
		} elseif ($notation === self::CASE_MACRO) {
			return str_replace('_', '-', strtolower($string));
		} elseif (
			($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && 
			preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)
		) {
			return strtolower(implode('-', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to macro case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in camel, pascal, snake, kebab or cobol case notation.<br>
	 * If given in macro case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to macro case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toMacroCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_MACRO) {
			return $string;
		} elseif ($notation === self::CASE_SNAKE) {
			return strtoupper($string);
		} elseif ($notation === self::CASE_KEBAB) {
			return str_replace('-', '_', strtoupper($string));
		} elseif ($notation === self::CASE_COBOL) {
			return str_replace('-', '_', $string);
		} elseif (
			($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && 
			preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)
		) {
			return strtoupper(implode('_', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to cobol case notation.
	 * 
	 * The given string can only be converted if it is already 
	 * in camel, pascal, snake, kebab or macro case notation.<br>
	 * If given in cobol case notation already, 
	 * then no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string
	 * <p>The string to convert.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Text\Exceptions\UnknownCaseNotation
	 * @return string|null
	 * <p>The given string converted to cobol case notation.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be converted.</p>
	 */
	final public static function toCobolCase(string $string, bool $no_throw = false): ?string
	{
		$notation = self::caseNotation($string, $no_throw);
		if ($notation === self::CASE_COBOL) {
			return $string;
		} elseif ($notation === self::CASE_KEBAB) {
			return strtoupper($string);
		} elseif ($notation === self::CASE_SNAKE) {
			return str_replace('_', '-', strtoupper($string));
		} elseif ($notation === self::CASE_MACRO) {
			return str_replace('_', '-', $string);
		} elseif (
			($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && 
			preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)
		) {
			return strtoupper(implode('-', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Check if a given string is a word.
	 * 
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param bool $unicode [default = false]
	 * <p>Check the given string as Unicode.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is a word.</p>
	 */
	final public static function isWord(string $string, bool $unicode = false): bool
	{
		return preg_match($unicode ? '/^[\pL\pN_]+(?:-[\pL\pN_]+)*$/u' : '/^\w+(?:-\w+)*$/', $string);
	}
	
	/**
	 * Localize a given message.
	 * 
	 * Placeholders may optionally be set in the message as <samp>{{placeholder}}</samp>, 
	 * and they must be exclusively composed of identifiers, which are defined as words which must start with a letter 
	 * (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), and may only contain letters 
	 * (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards 
	 * the given parameters, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.<br>
	 * <br>
	 * A context may also be given to differentiate the same message across distinct contexts.<br>
	 * All parameters are stringified.<br>
	 * <br>
	 * When calling this function, a phpDoc-like notation may be added above the call to describe both the message 
	 * and placeholders, and optionally provide an example of usage, to help the translator in fully understanding 
	 * the context of the message and thus provide the best translation possible.<br>
	 * <br>
	 * This notation is defined as follows:<br>
	 * <code>
	 * &#47;**<br>
	 * &nbsp;* &#64;description This is the message description.<br>
	 * &nbsp;* &#64;placeholder placeholder1 This is the placeholder1 description.<br>
	 * &nbsp;* &#64;placeholder placeholder2 This is the placeholder2 description.<br>
	 * &nbsp;* ...<br>
	 * &nbsp;* &#64;tags tag1 tag2 tag3 tag4 ... tagn<br>
	 * &nbsp;* &#64;example This is a message usage example.<br>
	 * &nbsp;*&#47;
	 * </code><br>
	 * <br>
	 * Once the PHP source files are scanned to look for the usage of this call, these descriptions and examples 
	 * will also be exported and saved into the resulting file with all the messages to localize.<br>
	 * The usage of newlines is fully respected during the parsing, however any newline not meant to be parsed 
	 * must be escaped by preceding it with a backslash character (<samp>\</samp>).<br>
	 * <br>
	 * As shown above, tags may also be provided, separated by whitespace (space, tab or newline), 
	 * to optionally be filtered by during the scan, in order to create files with only a specific desired subset 
	 * of all existing entries to localize.
	 * 
	 * @see \Dracodeum\Kit\Root\Locale
	 * @param string $message
	 * <p>The message to localize.</p>
	 * @param string|null $context [default = null]
	 * <p>The context to localize with.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Localize|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The localization of the given message.</p>
	 */
	final public static function localize(
		string $message, ?string $context = null, $text_options = null, $options = null
	): string
	{
		$text_options = TextOptions::coerce($text_options);
		$options = Options\Localize::coerce($options);
		return Locale::translate($message, $context, [
			'parameters' => $options->parameters,
			'info_scope' => $text_options->info_scope,
			'string_options' => $options->string_options,
			'stringifier' => $options->stringifier
		]);
	}
	
	/**
	 * Localize a given plural message.
	 * 
	 * Placeholders may optionally be set in the message as <samp>{{placeholder}}</samp>, and they must be exclusively 
	 * composed of identifiers, which are defined as words which must start with a letter 
	 * (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), and may only contain letters 
	 * (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards 
	 * the given parameters, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
	 * with no limit on the number of pointers chained.<br>
	 * <br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
	 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.<br>
	 * <br>
	 * A context may also be given to differentiate the same message across distinct contexts.<br>
	 * All parameters are stringified.<br>
	 * <br>
	 * When calling this function, a phpDoc-like notation may be added above the call to describe both the message 
	 * and placeholders, and optionally provide an example of usage, to help the translator in fully understanding 
	 * the context of the message and thus provide the best translation possible.<br>
	 * <br>
	 * This notation is defined as follows:<br>
	 * <code>
	 * &#47;**<br>
	 * &nbsp;* &#64;description This is the message description.<br>
	 * &nbsp;* &#64;placeholder placeholder1 This is the placeholder1 description.<br>
	 * &nbsp;* &#64;placeholder placeholder2 This is the placeholder2 description.<br>
	 * &nbsp;* ...<br>
	 * &nbsp;* &#64;tags tag1 tag2 tag3 tag4 ... tagn<br>
	 * &nbsp;* &#64;example This is a message usage example.<br>
	 * &nbsp;*&#47;
	 * </code><br>
	 * <br>
	 * Once the PHP source files are scanned to look for the usage of this call, 
	 * these descriptions and examples will also be exported and saved into the resulting file with all 
	 * the messages to localize.<br>
	 * The usage of newlines is fully respected during the parsing, however any newline not meant to be parsed 
	 * must be escaped by preceding it with a backslash character (<samp>\</samp>).<br>
	 * <br>
	 * As shown above, tags may also be provided, separated by whitespace (space, tab or newline), 
	 * to optionally be filtered by during the scan, in order to create files with only a specific desired subset 
	 * of all existing entries to localize.
	 * 
	 * @see \Dracodeum\Kit\Root\Locale
	 * @param string $message1
	 * <p>The message singular form to localize.</p>
	 * @param string $message2
	 * <p>The message plural form to localize.</p>
	 * @param float $number
	 * <p>The number to use.</p>
	 * @param string|null $number_placeholder [default = null]
	 * <p>The number placeholder to localize with.</p>
	 * @param string|null $context [default = null]
	 * <p>The context to localize with.</p>
	 * @param \Dracodeum\Kit\Options\Text|array|null $text_options [default = null]
	 * <p>The text options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @param \Dracodeum\Kit\Utilities\Text\Options\Plocalize|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or a set of <samp>name => value</samp> pairs.</p>
	 * @return string
	 * <p>The localization of the given plural message.</p>
	 */
	final public static function plocalize(
		string $message1, string $message2, float $number, ?string $number_placeholder = null, ?string $context = null,
		$text_options = null, $options = null
	): string
	{
		$text_options = TextOptions::coerce($text_options);
		$options = Options\Plocalize::coerce($options);
		return Locale::ptranslate($message1, $message2, $number, $number_placeholder, $context, [
			'parameters' => $options->parameters,
			'info_scope' => $text_options->info_scope,
			'string_options' => $options->string_options,
			'stringifier' => $options->stringifier
		]);
	}
	
	/**
	 * Format a given message.
	 * 
	 * In order to be perceived as the object of reference of a sentence ending in a colon (<samp>:</samp>), 
	 * such as:<br>
	 * <br>
	 * &nbsp; &nbsp; <i>"An error occurred with the following message: <b><samp>&lt;message&gt;</samp></b>"</i><br>
	 * <br>
	 * acting as a referenced example or message, while preserving and simplifying the natural expected text flow, 
	 * the given message is formatted as follows:<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; if the given message is not multilined, then it is simply uncapitalized, 
	 * in order to remain in the same line as a continuation of the previous sentence;<br>
	 * <br>
	 * &nbsp; &#8226; &nbsp; if the given message is multilined, 
	 * then it is prepended with 2 newline characters and indented, without any further modifications, 
	 * in order to break it away from the previous sentence, while still remaining visually clear that 
	 * it is the object of reference of the previous sentence.
	 * 
	 * @param string $message
	 * <p>The message to format.</p>
	 * @param bool $unicode [default = false]
	 * <p>Format as a Unicode message.</p>
	 * @param string $indentation_expression [default = "\t"]
	 * <p>The expression to indentate with.</p>
	 * @return string
	 * <p>The given message formatted.</p>
	 */
	final public static function formatMessage(
		string $message, bool $unicode = false, string $indentation_expression = "\t"
	): string
	{
		return self::multiline($message)
			? "\n\n" . self::indentate($message, 1, $indentation_expression)
			: self::uncapitalize($message, $unicode);
	}
}
