<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Options\Text as TextOptions;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text\{
	Options,
	Exceptions
};
use Feralygon\Kit\Root\Locale;

/**
 * Core text utility class.
 * 
 * This utility implements a set of methods used to manipulate and retrieve information from text or strings.
 * 
 * @since 1.0.0
 */
final class Text extends Utility
{
	//Public constants
	/** Do not quote strings during stringification (flag). */
	public const STRING_NO_QUOTES = 0x01;
	
	/** Always prepend the type for every value during stringification (flag). */
	public const STRING_PREPEND_TYPE = 0x02;
	
	/** "Or" conjunction for non-associative arrays during stringification (flag). */
	public const STRING_NONASSOC_CONJUNCTION_OR = 0x04;
	
	/** "Nor" conjunction for non-associative arrays during stringification (flag). */
	public const STRING_NONASSOC_CONJUNCTION_NOR = 0x08;
	
	/** "And" conjunction for non-associative arrays during stringification (flag). */
	public const STRING_NONASSOC_CONJUNCTION_AND = 0x10;
	
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
	
	
	
	//Final public static methods
	/**
	 * Check if a given string is empty.
	 * 
	 * A given string is considered to be empty if its value is either <code>null</code> or <code>''</code>.
	 * 
	 * @since 1.0.0
	 * @param string|null $string <p>The string to check.</p>
	 * @param bool $ignore_whitespace [default = false] <p>Ignore whitespace characters from the given string.<br>
	 * These characters are defined as follows:<br>
	 * &nbsp; &#8226; &nbsp; space (<code>' '</code>);<br>
	 * &nbsp; &#8226; &nbsp; tab (<code>"\t"</code>);<br>
	 * &nbsp; &#8226; &nbsp; new line (<code>"\n"</code>);<br>
	 * &nbsp; &#8226; &nbsp; carriage return (<code>"\r"</code>);<br>
	 * &nbsp; &#8226; &nbsp; NUL-byte (<code>"\0"</code>);<br>
	 * &nbsp; &#8226; &nbsp; vertical tab (<code>"\x0B"</code>).
	 * </p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is empty.</p>
	 */
	final public static function empty(?string $string, bool $ignore_whitespace = false) : bool
	{
		return !isset($string) || ($ignore_whitespace && trim($string) === '') || (!$ignore_whitespace && $string === '');
	}
	
	/**
	 * Generate a string from a given value.
	 * 
	 * The returning string represents the given value in order to be shown or printed out in messages.<br>
	 * Scalar values retain their full representation, while objects are represented only by their class names at most, and resources by their ids, 
	 * and arrays are represented as lists or structures depending on whether they are associative or not.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to generate from.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Stringify|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\StringifyUnsupportedValueType
	 * @return string <p>The generated string from the given value.</p>
	 */
	final public static function stringify($value, $text_options = null, $options = null) : string
	{
		//initialize
		$text_options = TextOptions::load($text_options);
		$options = Options\Stringify::load($options);
		$is_none = $text_options->info_scope === EInfoScope::NONE;
		$is_technical = $text_options->info_scope === EInfoScope::TECHNICAL;
		$is_enduser = $text_options->info_scope === EInfoScope::ENDUSER;
		
		//null
		if (!isset($value)) {
			return $is_enduser ? '' : '(null)';
		}
		
		//integer or float
		if (is_int($value) || is_float($value)) {
			$string = (string)$value;
			return ($options->flags & self::STRING_PREPEND_TYPE) ? (is_int($value) ? "(integer){$string}" : "(float){$string}") : $string;
		}
		
		//boolean
		if (is_bool($value)) {
			$string = '';
			if ($is_enduser) {
				if ($value) {
					/**
					 * @description Affirmative expression, as a text representation of a boolean "true" for the end-user, as in "enabled" or "supported".
					 * @tags core utility text stringify end-user
					 */
					$string = self::localize("yes", 'core.utilities.text', $text_options);
				} else {
					/**
					 * @description Negative expression, as a text representation of a boolean "false" for the end-user, as in "disabled" or "unsupported".
					 * @tags core utility text stringify end-user
					 */
					$string = self::localize("no", 'core.utilities.text', $text_options);
				}
			} else {
				$string = $value ? 'true' : 'false';
			}
			return ($options->flags & self::STRING_PREPEND_TYPE) ? "(boolean){$string}" : $string;
		}
		
		//string
		if (is_string($value)) {
			$string = ($options->flags & self::STRING_NO_QUOTES) ? $value : "\"{$value}\"";
			return ($options->flags & self::STRING_PREPEND_TYPE) ? "(string){$string}" : $string;
		}
		
		//object
		if (is_object($value)) {
			return '(object)' . ($is_enduser || $is_technical ? crc32(get_class($value)) : get_class($value));
		}
		
		//resource
		if (is_resource($value)) {
			return '(resource)#' . (int)$value;
		}
		
		//array
		if (is_array($value)) {
			//empty
			if (empty($value)) {
				return ($options->flags & self::STRING_PREPEND_TYPE) ? '(array)[]' : ($is_none ? '[]' : '');
			}
			
			//strings
			$strings = [];
			$is_associative = Data::isAssociative($value);
			foreach ($value as $k => $v) {
				$v_string = self::stringify($v, $text_options, $options);
				if ($is_associative) {
					if (!($options->flags & self::STRING_NO_QUOTES) && preg_match('/[\s:="{}\[\],]/', $k)) {
						$k = "\"{$k}\"";
					}
					$v_string = $is_none ? "{$k} = {$v_string}" : "{$k}: {$v_string}";
				}
				$strings[] = $v_string;
				unset($v_string);
			}
			
			//string
			$string = '';
			if ($is_associative) {
				$string = "\n" . self::indentate(implode("\n", $strings), $is_enduser ? 2 : 3, ' ') . "\n";
				if (!$is_enduser || ($options->flags & self::STRING_PREPEND_TYPE)) {
					$string = "{{$string}}";
				}
			} elseif ($options->flags & (self::STRING_NONASSOC_CONJUNCTION_OR | self::STRING_NONASSOC_CONJUNCTION_NOR | self::STRING_NONASSOC_CONJUNCTION_AND)) {
				$last_string = array_pop($strings);
				if (empty($strings)) {
					$string = $last_string;
				} else {
					$list_string = implode(', ', $strings);
					if ($options->flags & self::STRING_NONASSOC_CONJUNCTION_OR) {
						/**
						 * @description Usage of the "or" conjunction in a list, like so: "w, x, y or z".
						 * @placeholder list The comma separated list of elements.
						 * @placeholder last The last element of the list.
						 * @tags core utility text stringify
						 * @example "foo", "bar" or "zen"
						 */
						$string = self::localize("{{list}} or {{last}}", 'core.utilities.text', $text_options, [
							'parameters' => ['list' => $list_string, 'last' => $last_string]
						]);
					} elseif ($options->flags & self::STRING_NONASSOC_CONJUNCTION_NOR) {
						/**
						 * @description Usage of the "nor" conjunction in a list, like so: "w, x, y nor z".
						 * @placeholder list The comma separated list of elements.
						 * @placeholder last The last element of the list.
						 * @tags core utility text stringify
						 * @example "foo", "bar" nor "zen"
						 */
						$string = self::localize("{{list}} nor {{last}}", 'core.utilities.text', $text_options, [
							'parameters' => ['list' => $list_string, 'last' => $last_string]
						]);
					} elseif ($options->flags & self::STRING_NONASSOC_CONJUNCTION_AND) {
						/**
						 * @description Usage of the "and" conjunction in a list, like so: "w, x, y and z".
						 * @placeholder list The comma separated list of elements.
						 * @placeholder last The last element of the list.
						 * @tags core utility text stringify
						 * @example "foo", "bar" and "zen"
						 */
						$string = self::localize("{{list}} and {{last}}", 'core.utilities.text', $text_options, [
							'parameters' => ['list' => $list_string, 'last' => $last_string]
						]);
					}
				}
				if ($options->flags & self::STRING_PREPEND_TYPE) {
					$string = "[{$string}]";
				}
			} else {
				$string = implode(', ', $strings);
				if (!$is_enduser || ($options->flags & self::STRING_PREPEND_TYPE)) {
					$string = "[{$string}]";
				}
			}
			return ($options->flags & self::STRING_PREPEND_TYPE) ? "(array){$string}" : $string;
		}
		
		//exception
		throw new Exceptions\StringifyUnsupportedValueType(['value' => $value, 'type' => gettype($value)]);
	}
	
	/**
	 * Slugify a given string.
	 * 
	 * The process of slugification of a given string consists in converting all of its characters into the closest ones in 
	 * the ASCII alphanumeric range (<code>0-9</code>, <code>a-z</code> and <code>A-Z</code>), discarding all special characters and replacing word separator characters, 
	 * such as spaces, underscores, commas, periods and others of a similar type, by a given delimiter.<br>
	 * <br>
	 * The returning string is also trimmed and converted to lowercase by omission.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to slugify.</p>
	 * @param bool $keep_case [default = false] <p>Keep the original string case.</p>
	 * @param string $delimiter [default = '-'] <p>The delimiter character to be used between words.<br>
	 * It must be a single ASCII character.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\SlugifyInvalidDelimiter
	 * @return string <p>The slugified string from the given one.</p>
	 */
	final public static function slugify(string $string, bool $keep_case = false, string $delimiter = '-') : string
	{
		//validate
		if (strlen($delimiter) !== 1) {
			throw new Exceptions\SlugifyInvalidDelimiter(['delimiter' => $delimiter]);
		}
		
		//slugify
		$string = preg_replace('/[^\pL\d]+/iu', $delimiter, $string);
		$string = iconv(Locale::getEncoding(), 'ASCII//TRANSLIT', $string);
		$string = trim(preg_replace('/[^a-z\d' . preg_quote($delimiter, '/') . ']+/i', '', $string), $delimiter);
		return $keep_case ? $string : strtolower($string);
	}
	
	/**
	 * Unslugify a given string.
	 * 
	 * The process of unslugification of a given string consists in a best attempt to collapse all delimiter characters into spaces, 
	 * and keep all the non-delimiter characters intact (ASCII alphanumeric characters), which results into human-readable words 
	 * from the original slugified string, although they might not fully correspond to the original string before it was slugified.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to unslugify.</p>
	 * @param int $flags [default = 0x00] <p>The unslugification bitwise flags, which can be any combination of the following:<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNSLUG_CAPITALIZE_FIRST</code> : Capitalize the first word of the unslugified string.<br><br>
	 * &nbsp; &#8226; &nbsp; <code>self::UNSLUG_CAPITALIZE_ALL</code> : Capitalize all the words of the unslugified string.
	 * </p>
	 * @return string <p>The unslugified string from the given one.</p>
	 */
	final public static function unslugify(string $string, int $flags = 0x00) : string
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
	 * @since 1.0.0
	 * @param string $string <p>The string to bulletify.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Bulletify|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string <p>The bulletified string from the given one.</p>
	 */
	final public static function bulletify(string $string, $text_options = null, $options = null) : string
	{
		//initialize
		$text_options = TextOptions::load($text_options);
		$options = Options\Bulletify::load($options);
		
		//bulletify
		/**
		 * @description Text bulletification.
		 * @placeholder bullet The bullet character.
		 * @placeholder text The text to prepend the bullet to.
		 * @tags core utility text bulletify
		 * @example  &#8226; this is a bullet point;
		 */
		return self::localize(" {{bullet}} {{text}}", 'core.utilities.text', $text_options, [
			'parameters' => ['bullet' => $options->bullet, 'text' => $string]
		]);
	}
	
	/**
	 * Bulletify multiple given strings.
	 * 
	 * The process of bulletification of given strings consists in prepending a bullet character to each string, 
	 * turning the strings into bullet points, in a way to be safely localized for different languages.
	 * 
	 * @since 1.0.0
	 * @param string[] $strings <p>The strings to bulletify.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Mbulletify|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string[]|string <p>The bulletified strings from the given ones.<br>
	 * The original index association and sort of the strings array is preserved.<br>
	 * If <var>$options->merge</var> is set to <code>true</code>, a single merged string with all the given strings is returned instead, 
	 * with each string in a new line.</p>
	 */
	final public static function mbulletify(array $strings, $text_options = null, $options = null)
	{
		//initialize
		$text_options = TextOptions::load($text_options);
		$options = Options\Mbulletify::load($options);
		
		//bulletify
		foreach ($strings as &$string) {
			$string = self::bulletify($string, $text_options, ['bullet' => $options->bullet]);
		}
		unset($string);
		
		//punctuate
		if ($options->punctuate) {
			end($strings);
			$last_key = key($strings);
			foreach ($strings as $key => &$string) {
				if ($key === $last_key) {
					/**
					 * @description Bullet point text punctuation with period.
					 * @placeholder text The text to punctuate.
					 * @tags core utility text mbulletify
					 * @example  &#8226; this is the last bullet point.
					 */
					$string = self::localize("{{text}}.", 'core.utilities.text', $text_options, ['parameters' => ['text' => $string]]);
				} else {
					/**
					 * @description Bullet point text punctuation with semicolon.
					 * @placeholder text The text to punctuate.
					 * @tags core utility text mbulletify
					 * @example  &#8226; this is a bullet point;
					 */
					$string = self::localize("{{text}};", 'core.utilities.text', $text_options, ['parameters' => ['text' => $string]]);
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
	 * A given string is only considered to be an identifier as a word which starts with an ASCII letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and is exclusively composed by ASCII letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @param bool $extended [default = false] <p>Check as an extended identifier, in which dots may be used as delimiters between words to represent pointers.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is an identifier.</p>
	 */
	final public static function isIdentifier(string $string, bool $extended = false) : bool
	{
		return (bool)preg_match($extended ? '/^[a-z_]\w*(\.[a-z_]\w*)*$/i' : '/^[a-z_]\w*$/i', $string);
	}
	
	/**
	 * Check if a given string matches a given wildcard.
	 * 
	 * In a given wildcard, the <code>*</code> character matches any number and type of characters, including no characters at all, 
	 * and is also the only wildcard character recognized.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @param string $wildcard <p>The wildcard to match against.</p>
	 * @param bool $insensitive [default = false] <p>Perform a case-insensitive matching.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string matches the given wildcard.</p>
	 */
	final public static function isWildcardMatch(string $string, string $wildcard, bool $insensitive = false) : bool
	{
		$pattern = '/^' . implode('.*', array_map(function ($s) { return preg_quote($s, '/'); }, explode('*', $wildcard))) . '$/';
		if ($insensitive) {
			$pattern .= 'i';
		}
		return (bool)preg_match($pattern, $string);
	}
	
	/**
	 * Fill a given string with given parameters.
	 * 
	 * The process of filling a given string consists in replacing its placeholders by the given parameters.<br>
	 * <br>
	 * Placeholders must be set in the string as <code>{{placeholder}}</code>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.<br>
	 * <br>
	 * Any parameter given as neither a string nor a number is stringified.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to fill.</p>
	 * @param array $parameters <p>The parameters to fill the respective placeholders with, as <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Fill|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillInvalidPlaceholderMethodIdentifier
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillPlaceholderMethodIdentifierNotFound
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillPlaceholderPropertyIdentifierNotFound
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillPlaceholderKeyIdentifierNotFound
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillInvalidPlaceholderIdentifier
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\FillInvalidPlaceholder
	 * @return string <p>The given string filled with the given parameters.</p>
	 */
	final public static function fill(string $string, array $parameters, $text_options = null, $options = null) : string
	{
		//initialize
		$text_options = TextOptions::load($text_options);
		$options = Options\Fill::load($options);
		
		//tokenize
		$f_string = '';
		foreach (preg_split('/\{{2}(.*)\}{2}/U', $string, null, PREG_SPLIT_DELIM_CAPTURE) as $i => $token) {
			if ($i % 2 === 0) {
				$f_string .= $token;
			} elseif (self::isPlaceholder($token)) {
				//pointer
				$pointer = $parameters;
				foreach (explode('.', $token) as $identifier) {
					if ($identifier[-1] === ')') {
						$identifier = substr($identifier, 0, -2);
						if (!is_object($pointer)) {
							throw new Exceptions\FillInvalidPlaceholderMethodIdentifier(['placeholder' => $token, 'identifier' => "{$identifier}()"]);
						} elseif (!method_exists($pointer, $identifier)) {
							throw new Exceptions\FillPlaceholderMethodIdentifierNotFound(['placeholder' => $token, 'identifier' => "{$identifier}()"]);
						}
						$pointer = $pointer->$identifier();
					} elseif (is_object($pointer)) {
						if (!property_exists($pointer, $identifier)) {
							throw new Exceptions\FillPlaceholderPropertyIdentifierNotFound(['placeholder' => $token, 'identifier' => $identifier]);
						}
						$pointer = $pointer->$identifier;
					} elseif (is_array($pointer)) {
						if (!array_key_exists($identifier, $pointer)) {
							throw new Exceptions\FillPlaceholderKeyIdentifierNotFound(['placeholder' => $token, 'identifier' => $identifier]);
						}
						$pointer = $pointer[$identifier];
					} else {
						throw new Exceptions\FillInvalidPlaceholderIdentifier(['placeholder' => $token, 'identifier' => $identifier]);
					}
				}
				
				//stringify
				$pointer_string = null;
				if (isset($options->stringifier)) {
					$pointer_string = ($options->stringifier)($token, $pointer);
				}
				if (!isset($pointer_string)) {
					$pointer_string = is_string($pointer) ? $pointer : (is_numeric($pointer) ? (string)$pointer : self::stringify($pointer, $text_options, ['flags' => $options->string_flags]));
				}
				
				//finish
				$f_string .= $pointer_string;
				unset($pointer);
			} else {
				throw new Exceptions\FillInvalidPlaceholder(['placeholder' => $token]);
			}
		}
		return $f_string;
	}
	
	/**
	 * Fill a given plural string with given parameters.
	 * 
	 * The process of filling a given string consists in replacing its placeholders by the given parameters.<br>
	 * <br>
	 * Placeholders must be set in the string as <code>{{placeholder}}</code>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.<br>
	 * <br>
	 * Any parameter given as neither a string nor a number is stringified.
	 * 
	 * @since 1.0.0
	 * @param string $string1 <p>The string singular form to fill.</p>
	 * @param string $string2 <p>The string plural form to fill.</p>
	 * @param float|int $number <p>The number to use.</p>
	 * @param string|null $number_placeholder <p>The string number placeholder to fill with.</p>
	 * @param array $parameters <p>The parameters to fill the respective placeholders with, as <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Pfill|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string <p>The given plural string filled with the given parameters.</p>
	 */
	final public static function pfill(string $string1, string $string2, float $number, ?string $number_placeholder, array $parameters, $text_options = null, $options = null) : string
	{
		$text_options = TextOptions::load($text_options);
		$options = Options\Pfill::load($options);
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
	 * A given string is only considered to be a placeholder if its is exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * It may also have pointers to specific object properties or associative array values, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is a placeholder.</p>
	 */
	final public static function isPlaceholder(string $string) : bool
	{
		return (bool)preg_match('/^([a-z_]\w*(\(\))?)(\.[a-z_]\w*(\(\))?)*$/i', $string);
	}
	
	/**
	 * Check if a given string has a given placeholder.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @param string $placeholder <p>The placeholder to check for.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\InvalidPlaceholder
	 * @return bool <p>Boolean <samp>true</samp> if the given string has the given placeholder.</p>
	 */
	final public static function hasPlaceholder(string $string, string $placeholder) : bool
	{
		if (!self::isPlaceholder($placeholder)) {
			throw new Exceptions\InvalidPlaceholder(['placeholder' => $placeholder]);
		}
		return strpos($string, "{{{$placeholder}}}") !== false;
	}
	
	/**
	 * Check if a given string has all given placeholders.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @param string[] $placeholders <p>The placeholders to check for.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string has all the given placeholders.</p>
	 */
	final public static function hasAllPlaceholders(string $string, array $placeholders) : bool
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
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @param string[] $placeholders <p>The placeholders to check for.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string has any of the given placeholders.</p>
	 */
	final public static function hasAnyPlaceholders(string $string, array $placeholders) : bool
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
	 * Placeholders must be present in the given string as <code>{{placeholder}}</code>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to get from.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\InvalidPlaceholder
	 * @return string[] <p>The placeholders from the given string.</p>
	 */
	final public static function getPlaceholders(string $string) : array
	{
		$placeholders = [];
		if (preg_match_all('/\{{2}(.*)\}{2}/U', $string, $matches) > 0) {
			foreach ($matches[1] as $placeholder) {
				if (!self::isPlaceholder($placeholder)) {
					throw new Exceptions\InvalidPlaceholder(['placeholder' => $placeholder]);
				}
				$placeholders[] = $placeholder;
			}
		}
		return $placeholders;
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
	 * @since 1.0.0
	 * @see https://php.net/manual/en/reference.pcre.pattern.syntax.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.modifiers.php
	 * @param string $string <p>The string to parse from.</p>
	 * @param string[] $fields_patterns <p>The fields regular expression patterns to parse with, as <code>field => pattern</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Parse|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return array|null <p>The parsed data from the given string, as <samp>field => value</samp> pairs, or <samp>null</samp> if no data could be parsed.</p>
	 */
	final public static function parse(string $string, array $fields_patterns, $options = null) : ?array
	{
		return self::mparse([$string], $fields_patterns, Options\Parse::load($options))[0] ?? null;
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
	 * @since 1.0.0
	 * @see https://php.net/manual/en/reference.pcre.pattern.syntax.php
	 * @see https://php.net/manual/en/reference.pcre.pattern.modifiers.php
	 * @param string[] $strings <p>The strings to parse from.</p>
	 * @param string[] $fields_patterns <p>The fields regular expression patterns to parse with, as <code>field => pattern</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Mparse|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\MparseInvalidString
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\MparseInvalidFieldPattern
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\MparseInvalidDelimiterPattern
	 * @return array <p>The parsed data from the given strings as an array of <samp>field => value</samp> pairs per string,
	 * or <samp>null</samp> per string if <var>$options->keep_nulls = true</var> and no data could be parsed from it.<br>
	 * The original index association and sort of the strings array is preserved.</p>
	 */
	final public static function mparse(array $strings, array $fields_patterns, $options = null) : array
	{
		//initialize
		$options = Options\Mparse::load($options);
		$pattern_delimiter = $options->pattern_delimiter;
		$delimiter_pattern = $options->delimiter_pattern;
		$pattern_modifiers = $options->pattern_modifiers;
		
		//validate strings
		foreach ($strings as &$string) {
			if (!Type::evaluateString($string)) {
				throw new Exceptions\MparseInvalidString(['string' => $string]);
			}
		}
		unset($string);
		
		//validate fields patterns
		if (empty($fields_patterns)) {
			return $options->keep_nulls ? array_fill_keys(array_keys($strings), null) : [];
		} else {
			foreach ($fields_patterns as $field => $pattern) {
				if (!is_string($pattern) || preg_match($pattern_delimiter . $pattern . $pattern_delimiter, null) === false) {
					throw new Exceptions\MparseInvalidFieldPattern(['field' => $field, 'pattern' => $pattern]);
				}
			}
		}
		
		//validate pattern
		if (preg_match($pattern_delimiter . $delimiter_pattern . $pattern_delimiter, null) === false) {
			throw new Exceptions\MparseInvalidDelimiterPattern(['pattern' => $delimiter_pattern]);
		}
		
		//prepare
		$group = 1;
		$groups_fields = [];
		foreach ($fields_patterns as $field => $pattern) {
			$groups_fields[$group] = $field;
			$group += preg_match_all('/(^|[^\\\\])\(/', $pattern) + 1;
		}
		unset($group);
		$pattern = "{$pattern_delimiter}^(" . implode("){$delimiter_pattern}(", $fields_patterns) . ")\${$pattern_delimiter}{$pattern_modifiers}";
		
		//parse
		$strings_fields_values = [];
		foreach ($strings as $key => $string) {
			if (preg_match($pattern, $string, $matches)) {
				foreach ($groups_fields as $group => $field) {
					$strings_fields_values[$key][$field] = $matches[$group] ?? null;
				}
			} elseif ($options->keep_nulls) {
				$strings_fields_values[$key] = null;
			}
		}
		
		//return
		return $strings_fields_values;
	}
	
	/**
	 * Convert the first letter of a given string to lowercase.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.lcfirst.php
	 * @param string $string <p>The string to convert.</p>
	 * @param bool $unicode [default = false] <p>Convert as an Unicode string.</p>
	 * @return string <p>The given string with the first letter converted to lowercase.</p>
	 */
	final public static function lcfirst(string $string, bool $unicode = false) : string
	{
		if ($unicode) {
			$encoding = Locale::getEncoding();
			return mb_strtolower(mb_substr($string, 0, 1, $encoding), $encoding) . mb_substr($string, 1, null, $encoding);
		}
		return lcfirst($string);
	}
	
	/**
	 * Convert the first letter of a given string to uppercase.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.ucfirst.php
	 * @param string $string <p>The string to convert.</p>
	 * @param bool $unicode [default = false] <p>Convert as an Unicode string.</p>
	 * @return string <p>The given string with the first letter converted to uppercase.</p>
	 */
	final public static function ucfirst(string $string, bool $unicode = false) : string
	{
		if ($unicode) {
			$encoding = Locale::getEncoding();
			return mb_strtoupper(mb_substr($string, 0, 1, $encoding), $encoding) . mb_substr($string, 1, null, $encoding);
		}
		return ucfirst($string);
	}
	
	/**
	 * Calculate the length of a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to calculate from.</p>
	 * @param bool $unicode [default = false] <p>Calculate as an Unicode string.</p>
	 * @return int <p>The length of the given string.</p>
	 */
	final public static function length(string $string, bool $unicode = false) : int
	{
		return $unicode ? mb_strlen($string, Locale::getEncoding()) : strlen($string);
	}
	
	/**
	 * Convert a given string to uppercase.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to convert.</p>
	 * @param bool $unicode [default = false] <p>Convert as an Unicode string.</p>
	 * @return string <p>The given string converted to uppercase.</p>
	 */
	final public static function upper(string $string, bool $unicode = false) : string
	{
		return $unicode ? mb_strtoupper($string, Locale::getEncoding()) : strtoupper($string);
	}
	
	/**
	 * Convert a given string to lowercase.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to convert.</p>
	 * @param bool $unicode [default = false] <p>Convert as an Unicode string.</p>
	 * @return string <p>The given string converted to lowercase.</p>
	 */
	final public static function lower(string $string, bool $unicode = false) : string
	{
		return $unicode ? mb_strtolower($string, Locale::getEncoding()) : strtolower($string);
	}
	
	/**
	 * Retrieve sub-string from a given string from a given starting index.
	 * 
	 * @since 1.0.0
	 * @see https://php.net/manual/en/function.substr.php
	 * @param string $string <p>The string to retrieve from.</p>
	 * @param int $start <p>The starting index to retrieve from, with <code>0</code> corresponding to the first character.<br>
	 * If negative, it is interpreted as starting at the end of the given string, with the last character corresponding to <code>-1</code>.</p>
	 * @param int|null $length [default = null] <p>The maximum length of the returning sub-string.<br>
	 * If negative, it is interpreted as the number of characters to remove from the end of the given string.<br>
	 * If not set, it is interpreted as being the exact length of the given string.</p>
	 * @param bool $unicode [default = false] <p>Retrieve as an Unicode string.</p>
	 * @return string <p>The sub-string from the given string from the given starting index.</p>
	 */
	final public static function sub(string $string, int $start, ?int $length = null, bool $unicode = false) : string
	{
		return $unicode ? mb_substr($string, $start, $length, Locale::getEncoding()) : (isset($length) ? substr($string, $start, $length) : substr($string, $start));
	}
	
	/**
	 * Capitalize a given string.
	 * 
	 * The process of capitalization of a given string consists in converting the first character from its first word to uppercase, but only if it is safe to do so.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to capitalize.</p>
	 * @param bool $unicode [default = false] <p>Capitalize as an Unicode string.</p>
	 * @return string <p>The given string capitalized.</p>
	 */
	final public static function capitalize(string $string, bool $unicode = false) : string
	{
		if (preg_match($unicode ? '/^([^\pL]*)(\pL[\pL\-]*)(.*)$/usm' : '/^([^a-z]*)([a-z][a-z\-]*)(.*)$/ism', $string, $matches) && self::lower($matches[2], $unicode) === $matches[2]) {
			return $matches[1] . self::ucfirst($matches[2], $unicode) . $matches[3];
		}
		return $string;
	}
	
	/**
	 * Uncapitalize a given string.
	 * 
	 * The process of uncapitalization of a given string consists in converting the first character from its first word to lowercase, but only if it is safe to do so.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to uncapitalize.</p>
	 * @param bool $unicode [default = false] <p>Uncapitalize as an Unicode string.</p>
	 * @return string <p>The given string uncapitalized.</p>
	 */
	final public static function uncapitalize(string $string, bool $unicode = false) : string
	{
		if (preg_match($unicode ? '/^([^\pL]*)(\pL[\pL\-]*)(.*)$/usm' : '/^([^a-z]*)([a-z][a-z\-]*)(.*)$/ism', $string, $matches) && self::ucfirst(self::lower($matches[2], $unicode), $unicode) === $matches[2]) {
			return $matches[1] . self::lcfirst($matches[2], $unicode) . $matches[3];
		}
		return $string;
	}
	
	/**
	 * Calculate the distance between two given strings.
	 * 
	 * The returning distance between the two given strings is calculated by using the Levenshtein distance algorithm, 
	 * which defines the distance as the minimum number of inserts, deletes and substitutions which need to take place 
	 * to transform one string into another.<br>
	 * <br>
	 * Alternatively, its Damerau variation (Damerau-Levenshtein) can be used to also consider transpositions of 2 adjacent characters 
	 * to result into a distance of 1 (1 transposition) instead of 2 (2 substitutions).
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Levenshtein_distance
	 * @see https://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
	 * @param string $string1 <p>The first string, to calculate from.</p>
	 * @param string $string2 <p>The second string, to calculate from.</p>
	 * @param bool $damerau [default = false] <p>Use the Damerau variation of the algorithm (Damerau-Levenshtein).</p>
	 * @param bool $insensitive [default = false] <p>Perform a case-insensitive calculation.</p>
	 * @param bool $unicode [default = false] <p>Calculate the distance as Unicode.</p>
	 * @return int <p>The distance between the two given strings.</p>
	 */
	final public static function distance(string $string1, string $string2, bool $damerau = false, bool $insensitive = false, bool $unicode = false) : int
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
		$max_distance = max($length1, $length2);
		for ($i2 = 1; $i2 <= $length2; $i2++) {
			for ($i1 = 1; $i1 <= $length1; $i1++) {
				$cost = $chars1[$i1 - 1] === $chars2[$i2 - 1] ? 0 : 1;
				$matrix[$i1][$i2] = min($matrix[$i1 - 1][$i2] + 1, $matrix[$i1][$i2 - 1] + 1, $matrix[$i1 - 1][$i2 - 1] + $cost);
				if ($damerau && $i1 > 1 && $i2 > 1 && $chars1[$i1 - 1] === $chars2[$i2 - 2] && $chars1[$i1 - 2] === $chars2[$i2 - 1]) {
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
	 * @since 1.0.0
	 * @param string $string <p>The string to truncate.</p>
	 * @param int $length <p>The length to truncate to.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Truncate|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\TruncateInvalidLength
	 * @return string <p>The given string truncated to the given length.</p>
	 */
	final public static function truncate(string $string, int $length, $options = null) : string
	{
		//initialize
		$options = Options\Truncate::load($options);
		$unicode = $options->unicode;
		$end_string = '';
		if ($length < 0) {
			throw new Exceptions\TruncateInvalidLength(['length' => $length]);
		} elseif (self::length($string, $unicode) <= $length) {
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
		if ($options->keep_sentences && preg_match_all($unicode ? '/\P{Po}+\p{Po}+/u' : '/[^\.\?\!]+[\.\?\!]+/', $string, $matches) > 0) {
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
		
		//words
		if ($options->keep_words) {
			$t_length = 0;
			$t_string = '';
			foreach (preg_split($unicode ? '/([^\pL\pN_\-]+)/u' : '/([^\w\-]+)/i', $string, null, PREG_SPLIT_DELIM_CAPTURE) as $part) {
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
	 * @since 1.0.0
	 * @param string $string <p>The string to indentate.</p>
	 * @param int $level [default = 1] <p>The level to indentate with.<br>
	 * It must be greater than or equal to <code>0</code>.</p>
	 * @param string $character [default = "\t"] <p>The character to indentate with.<br>
	 * It must be a single ASCII character.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\IndentateInvalidLevel
	 * @throws \Feralygon\Kit\Core\Utilities\Text\Exceptions\IndentateInvalidCharacter
	 * @return string <p>The given string indentated.</p>
	 */
	final public static function indentate(string $string, int $level = 1, string $character = "\t") : string
	{
		if ($level < 0) {
			throw new Exceptions\IndentateInvalidLevel(['level' => $level]);
		} elseif (self::length($character) !== 1) {
			throw new Exceptions\IndentateInvalidCharacter(['character' => $character]);
		} elseif ($level === 0 || $character === '') {
			return $string;
		}
		return preg_replace('/^/mu', str_repeat($character, $level), $string);
	}
	
	/**
	 * Check if a given string is multiline.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is multiline.</p>
	 */
	final public static function isMultiline(string $string) : bool
	{
		return strpos($string, "\n") !== false;
	}
	
	/**
	 * Check if a given string is in camel case notation.
	 * 
	 * A given string is only considered to be in camel case notation if it starts with a lowercase character 
	 * from <code>a</code> to <code>z</code> and is only composed by ASCII alphanumeric characters (<code>0-9</code>, <code>a-z</code> and <code>A-Z</code>).<br>
	 * <br>
	 * The strings <code>foo</code> and <code>fooBar</code> are two examples of camel case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in camel case notation.</p>
	 */
	final public static function isCamelCase(string $string) : bool
	{
		return (bool)preg_match('/^[a-z][a-zA-Z\d]*$/', $string);
	}
	
	/**
	 * Check if a given string is in pascal case notation.
	 * 
	 * A given string is only considered to be in pascal case notation if it starts with an uppercase character 
	 * from <code>A</code> to <code>Z</code> and is only composed by ASCII alphanumeric characters (<code>0-9</code>, <code>a-z</code> and <code>A-Z</code>).<br>
	 * <br>
	 * The strings <code>Foo</code> and <code>FooBar</code> are two examples of pascal case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in pascal case notation.</p>
	 */
	final public static function isPascalCase(string $string) : bool
	{
		return (bool)preg_match('/^[A-Z][a-zA-Z\d]*$/', $string);
	}
	
	/**
	 * Check if a given string is in snake case notation.
	 * 
	 * A given string is only considered to be in snake case notation if it starts with a lowercase character 
	 * from <code>a</code> to <code>z</code> and is only composed by lowercase ASCII alphanumeric characters (<code>0-9</code> and <code>a-z</code>), 
	 * with words delimited by a single underscore (<code>_</code>) between them.<br>
	 * <br>
	 * The strings <code>foo</code> and <code>foo_bar</code> are two examples of snake case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in snake case notation.</p>
	 */
	final public static function isSnakeCase(string $string) : bool
	{
		return (bool)preg_match('/^[a-z][a-z\d]*(_[a-z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in kebab case notation.
	 * 
	 * A given string is only considered to be in kebab case notation if it starts with a lowercase character 
	 * from <code>a</code> to <code>z</code> and is only composed by lowercase ASCII alphanumeric characters (<code>0-9</code> and <code>a-z</code>), 
	 * with words delimited by a single hyphen (<code>-</code>) between them.<br>
	 * <br>
	 * The strings <code>foo</code> and <code>foo-bar</code> are two examples of kebab case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in kebab case notation.</p>
	 */
	final public static function isKebabCase(string $string) : bool
	{
		return (bool)preg_match('/^[a-z][a-z\d]*(-[a-z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in macro case notation.
	 * 
	 * A given string is only considered to be in macro case notation if it starts with an uppercase character 
	 * from <code>A</code> to <code>Z</code> and is only composed by uppercase ASCII alphanumeric characters (<code>0-9</code> and <code>A-Z</code>), 
	 * with words delimited by a single underscore (<code>_</code>) between them.<br>
	 * <br>
	 * The strings <code>FOO</code> and <code>FOO_BAR</code> are two examples of macro case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in macro case notation.</p>
	 */
	final public static function isMacroCase(string $string) : bool
	{
		return (bool)preg_match('/^[A-Z][A-Z\d]*(_[A-Z\d]+)*$/', $string);
	}
	
	/**
	 * Check if a given string is in cobol case notation.
	 * 
	 * A given string is only considered to be in cobol case notation if it starts with an uppercase character 
	 * from <code>A</code> to <code>Z</code> and is only composed by uppercase ASCII alphanumeric characters (<code>0-9</code> and <code>A-Z</code>), 
	 * with words delimited by a single hyphen (<code>-</code>) between them.<br>
	 * <br>
	 * The strings <code>FOO</code> and <code>FOO-BAR</code> are two examples of cobol case notation.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>String to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given string is in cobol case notation.</p>
	 */
	final public static function isCobolCase(string $string) : bool
	{
		return (bool)preg_match('/^[A-Z][A-Z\d]*(-[A-Z\d]+)*$/', $string);
	}
	
	/**
	 * Retrieve case notation from a given string.
	 * 
	 * The returning case notation from the given string is recognized by checking mostly the case of each character, and only 
	 * strings exclusively composed by ASCII alphanumeric characters (<code>0-9</code>, <code>a-z</code> and <code>A-Z</code>), 
	 * optionally with underscore (<code>_</code>) or hyphen (<code>-</code>) as delimiters, are considered.<br>
	 * <br>
	 * The following are some examples of each notation:<br>
	 * &nbsp; &#8226; &nbsp; <code>foo</code> and <code>fooBar</code> are in camel case notation.<br>
	 * &nbsp; &#8226; &nbsp; <code>Foo</code> and <code>FooBar</code> are in pascal case notation.<br>
	 * &nbsp; &#8226; &nbsp; <code>foo</code> and <code>foo_bar</code> are in snake case notation.<br>
	 * &nbsp; &#8226; &nbsp; <code>foo</code> and <code>foo-bar</code> are in kebab case notation.<br>
	 * &nbsp; &#8226; &nbsp; <code>FOO</code> and <code>FOO_BAR</code> are in macro case notation.<br>
	 * &nbsp; &#8226; &nbsp; <code>FOO</code> and <code>FOO-BAR</code> are in cobol case notation.<br>
	 * <br>
	 * Some strings may become ambiguous such as <samp>foo</samp> which is simultaneously in snake, kebab and camel case notations, 
	 * therefore the desambiguation is solved by performing the internal checks in the following order:<br>
	 * 1 - Snake case notation check;<br>
	 * 2 - Kebab case notation check;<br>
	 * 3 - Macro case notation check;<br>
	 * 4 - Cobol case notation check;<br>
	 * 5 - Camel case notation check;<br>
	 * 6 - Pascal case notation check.<br>
	 * <br>
	 * In other words, <code>foo</code> will be recognized to be in snake case notation only, since the snake case check 
	 * is performed before the kebab and camel case ones.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to get from.</p>
	 * @return string|null <p>The case notation of a given string as:<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_SNAKE</samp> for snake case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_KEBAB</samp> for kebab case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_MACRO</samp> for macro case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_COBOL</samp> for cobol case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_CAMEL</samp> for camel case;<br>
	 * &nbsp; &#8226; &nbsp; the value of <samp>self::CASE_PASCAL</samp> for pascal case;<br>
	 * or <samp>null</samp> if no notation was recognized.</p>
	 */
	final public static function caseNotation(string $string) : ?string
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
		}
		return null;
	}
	
	/**
	 * Convert a given string to camel case notation.
	 * 
	 * The given string can only be converted if it is already in pascal, snake, kebab, macro or cobol case notation.<br>
	 * If given in camel case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to camel case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toCamelCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
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
	 * The given string can only be converted if it is already in camel, snake, kebab, macro or cobol case notation.<br>
	 * If given in pascal case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to pascal case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toPascalCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
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
	 * The given string can only be converted if it is already in camel, pascal, kebab, macro or cobol case notation.<br>
	 * If given in snake case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to snake case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toSnakeCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
		if ($notation === self::CASE_SNAKE) {
			return $string;
		} elseif ($notation === self::CASE_MACRO) {
			return strtolower($string);
		} elseif ($notation === self::CASE_KEBAB) {
			return str_replace('-', '_', $string);
		} elseif ($notation === self::CASE_COBOL) {
			return str_replace('-', '_', strtolower($string));
		} elseif (($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)) {
			return strtolower(implode('_', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to kebab case notation.
	 * 
	 * The given string can only be converted if it is already in camel, pascal, snake, macro or cobol case notation.<br>
	 * If given in kebab case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to kebab case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toKebabCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
		if ($notation === self::CASE_KEBAB) {
			return $string;
		} elseif ($notation === self::CASE_COBOL) {
			return strtolower($string);
		} elseif ($notation === self::CASE_SNAKE) {
			return str_replace('_', '-', $string);
		} elseif ($notation === self::CASE_MACRO) {
			return str_replace('_', '-', strtolower($string));
		} elseif (($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)) {
			return strtolower(implode('-', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to macro case notation.
	 * 
	 * The given string can only be converted if it is already in camel, pascal, snake, kebab or cobol case notation.<br>
	 * If given in macro case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to macro case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toMacroCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
		if ($notation === self::CASE_MACRO) {
			return $string;
		} elseif ($notation === self::CASE_SNAKE) {
			return strtoupper($string);
		} elseif ($notation === self::CASE_KEBAB) {
			return str_replace('-', '_', strtoupper($string));
		} elseif ($notation === self::CASE_COBOL) {
			return str_replace('-', '_', $string);
		} elseif (($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)) {
			return strtoupper(implode('_', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Convert a given string to cobol case notation.
	 * 
	 * The given string can only be converted if it is already in camel, pascal, snake, kebab or macro case notation.<br>
	 * If given in cobol case notation already, no conversion is performed whatsoever and the same string is returned.
	 * 
	 * @since 1.0.0
	 * @see https://en.wikipedia.org/wiki/Snake_case
	 * @see https://en.wikipedia.org/wiki/Camel_case
	 * @see https://en.wikipedia.org/wiki/PascalCase
	 * @see https://en.wikipedia.org/wiki/Naming_convention_(programming)
	 * @param string $string <p>The string to convert.</p>
	 * @return string|null <p>The given string converted to cobol case notation or <samp>null</samp> if it could not be converted.</p>
	 */
	final public static function toCobolCase(string $string) : ?string
	{
		$notation = self::caseNotation($string);
		if ($notation === self::CASE_COBOL) {
			return $string;
		} elseif ($notation === self::CASE_KEBAB) {
			return strtoupper($string);
		} elseif ($notation === self::CASE_SNAKE) {
			return str_replace('_', '-', strtoupper($string));
		} elseif ($notation === self::CASE_MACRO) {
			return str_replace('_', '-', $string);
		} elseif (($notation === self::CASE_CAMEL || $notation === self::CASE_PASCAL) && preg_match_all('/[A-Z][a-z\d]*/', ucfirst($string), $matches)) {
			return strtoupper(implode('-', $matches[0]));
		}
		return null;
	}
	
	/**
	 * Localize a given message.
	 * 
	 * Unlike the <code>translate</code> method from the root locale class <code>\Feralygon\Kit\Root\Locale</code>, 
	 * the returning message is only actually translated depending on the given text options, in other words, 
	 * this function is meant to be used with any message which is only meant to be translated if such is explicitly demanded by the callee through text options.<br>
	 * <br>
	 * Placeholders may optionally be set in the message as <code>{{placeholder}}</code>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.<br>
	 * <br>
	 * A context may also be given to differentiate the same message across distinct contexts.<br>
	 * Any parameter given as neither a string nor a number is stringified.<br>
	 * <br>
	 * When calling this function, a PHPDoc-like notation may be added above the call to describe both the message and placeholders,
	 * as well as optionally provide an example of usage, to help the translator in fully understanding the context of the message 
	 * and thus provide the best translation possible.<br>
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
	 * these descriptions and examples will also be retrieved and saved into the resulting file with all the messages to translate.<br>
	 * As shown above, tags may also be provided, separated by whitespace (space, tab or newline), to optionally be filtered by during the scan, 
	 * in order to create files with only a specific desired subset of all existing entries to translate.
	 * 
	 * @since 1.0.0
	 * @param string $message <p>The message to localize.</p>
	 * @param string|null $context [default = null] <p>The message context to localize with.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Localize|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string <p>The localization of the given message.</p>
	 */
	final public static function localize(string $message, ?string $context = null, $text_options = null, $options = null) : string
	{
		$text_options = TextOptions::load($text_options);
		$options = Options\Localize::load($options);
		if ($text_options->translate) {
			return Locale::translate($message, $context, [
				'parameters' => $options->parameters,
				'info_scope' => $text_options->info_scope,
				'string_flags' => $options->string_flags,
				'stringifier' => $options->stringifier,
				'language' => $text_options->language
			]);
		} elseif (!empty($options->parameters)) {
			return self::fill($message, $options->parameters, ['info_scope' => $text_options->info_scope], [
				'string_flags' => $options->string_flags,
				'stringifier' => $options->stringifier
			]);
		}
		return $message;
	}
	
	/**
	 * Localize a given plural message.
	 * 
	 * Unlike the <code>ptranslate</code> method from the root locale class <code>\Feralygon\Kit\Root\Locale</code>, 
	 * the returning message is only actually translated depending on the given text options, in other words, 
	 * this function is meant to be used with any message which is only meant to be translated if such is explicitly demanded by the callee through text options.<br>
	 * <br>
	 * Placeholders may optionally be set in the message as <code>{{placeholder}}</code>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) or underscore (<code>_</code>), 
	 * and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <code>{{object.property}}</code>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
	 * but they cannot be given any custom parameters.<br>
	 * <br>
	 * A context may also be given to differentiate the same message across distinct contexts.<br>
	 * Any parameter given as neither a string nor a number is stringified.<br>
	 * <br>
	 * When calling this function, a PHPDoc-like notation may be added above the call to describe both the message and placeholders,
	 * as well as optionally provide an example of usage, to help the translator in fully understanding the context of the message 
	 * and thus provide the best translation possible.<br>
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
	 * these descriptions and examples will also be retrieved and saved into the resulting file with all the messages to translate.<br>
	 * As shown above, tags may also be provided, separated by whitespace (space, tab or newline), to optionally be filtered by during the scan, 
	 * in order to create files with only a specific desired subset of all existing entries to translate.
	 * 
	 * @since 1.0.0
	 * @param string $message1 <p>The message singular form to localize.</p>
	 * @param string $message2 <p>The message plural form to localize.</p>
	 * @param float|int $number <p>The number to use.</p>
	 * @param string|null $number_placeholder <p>The message number placeholder to localize with.</p>
	 * @param string|null $context [default = null] <p>The message context to localize with.</p>
	 * @param \Feralygon\Kit\Core\Options\Text|array|null $text_options [default = null] <p>The text options to use, as an instance or <code>name => value</code> pairs.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Text\Options\Plocalize|array|null $options [default = null] <p>Additional options, as an instance or <code>name => value</code> pairs.</p>
	 * @return string <p>The localization of the given plural message.</p>
	 */
	final public static function plocalize(string $message1, string $message2, float $number, ?string $number_placeholder, ?string $context = null, $text_options = null, $options = null) : string
	{
		$text_options = TextOptions::load($text_options);
		$options = Options\Plocalize::load($options);
		if ($text_options->translate) {
			return Locale::ptranslate($message1, $message2, $number, $number_placeholder, $context, [
				'parameters' => $options->parameters,
				'info_scope' => $text_options->info_scope,
				'string_flags' => $options->string_flags,
				'stringifier' => $options->stringifier,
				'language' => $text_options->language
			]);
		} elseif (isset($number_placeholder) || !empty($options->parameters)) {
			return self::pfill($message1, $message2, $number, $number_placeholder, $options->parameters, ['info_scope' => $text_options->info_scope], [
				'string_flags' => $options->string_flags,
				'stringifier' => $options->stringifier
			]);
		}
		return abs($number) === 1.0 ? $message1 : $message2;
	}
}
