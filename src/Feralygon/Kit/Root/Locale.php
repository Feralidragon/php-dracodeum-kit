<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root;

use Feralygon\Kit\Root\Locale\{
	Options,
	Exceptions
};
use Feralygon\Kit\Core\Utilities\Text as UText;

/**
 * Root locale class.
 * 
 * This class is used to statically handle localization, such as encoding and text translations into different languages.
 * 
 * @since 1.0.0
 */
final class Locale
{
	//Private static properties
	/** @var string */
	private static $encoding = 'UTF-8';
	
	
	
	//Final public static methods
	/**
	 * Get encoding.
	 * 
	 * @since 1.0.0
	 * @return string <p>The encoding.</p>
	 */
	final public static function getEncoding() : string
	{
		return self::$encoding;
	}
	
	/**
	 * Set encoding.
	 * 
	 * @since 1.0.0
	 * @param string $encoding <p>The encoding to set.</p>
	 * @throws \Feralygon\Kit\Root\Locale\Exceptions\InvalidEncoding
	 * @return void
	 */
	final public static function setEncoding(string $encoding) : void
	{
		$encodings = mb_list_encodings();
		if (!in_array($encoding, $encodings, true)) {
			throw new Exceptions\InvalidEncoding(['encoding' => $encoding, 'encodings' => $encodings]);
		}
		self::$encoding = $encoding;
	}
	
	/**
	 * Evaluate a given value as a language.
	 * 
	 * Only the following types and formats can be evaluated into a language:<br>
	 * &nbsp; &#8226; &nbsp; an ISO 639 code string, such as: <code>"en"</code> or <code>"eng"</code> for English;<br>
	 * &nbsp; &#8226; &nbsp; an ISO 639 code string with an ISO 3166-1 country code, such as: <code>"en-US"</code> or <code>"eng-USA"</code> for English from United States of America
	 * (both underscores (<samp>_</samp>) and hyphens (<samp>-</samp>) are allowed, as well as any combination of code types).<br>
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a language.</p>
	 */
	final public static function evaluateLanguage(&$value, bool $nullable = false) : bool
	{
		return isset($value) ? is_string($value) && (bool)preg_match('/^[a-z]{2,3}([_\-][A-Z]{2,3})?$/', $value) : $nullable;
	}
	
	/**
	 * Translate a given message.
	 * 
	 * Placeholders may optionally be set in the message as <samp>{{placeholder}}</samp>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <samp>{{object.property}}</samp>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, the identifiers are interpreted as getter method calls, 
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
	 * @param string $message <p>The message to translate.</p>
	 * @param string|null $context [default = null] <p>The message context to translate with.</p>
	 * @param \Feralygon\Kit\Root\Locale\Options\Translate|array|null $options [default = null] <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The translation of the given message.</p>
	 */
	final public static function translate(string $message, ?string $context = null, $options = null) : string
	{
		//initialize
		$options = Options\Translate::load($options);
		
		
		//TODO: implement the actual translation
		
		
		//parameters
		if (!empty($options->parameters)) {
			$message = UText::fill($message, $options->parameters, [
				'info_scope' => $options->info_scope,
				'translate' => true,
				'language' => $options->language
			], [
				'string_flags' => $options->string_flags,
				'stringifier' => $options->stringifier
			]);
		}
		
		//return
		return $message;
	}
	
	/**
	 * Translate a given plural message.
	 * 
	 * Placeholders may optionally be set in the message as <samp>{{placeholder}}</samp>, and they must be exclusively composed by identifiers, 
	 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) or underscore (<samp>_</samp>), 
	 * and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
	 * <br>
	 * They may also be used as pointers to specific object properties or associative array values towards the given parameters, by using a dot between identifiers, 
	 * such as <samp>{{object.property}}</samp>, with no limit on the number of pointers chained.<br>
	 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, the identifiers are interpreted as getter method calls, 
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
	 * @param string $message1 <p>The message singular form to translate.</p>
	 * @param string $message2 <p>The message plural form to translate.</p>
	 * @param float|int $number <p>The number to use.</p>
	 * @param string|null $number_placeholder <p>The message number placeholder to translate with.</p>
	 * @param string|null $context [default = null] <p>The message context to translate with.</p>
	 * @param \Feralygon\Kit\Root\Locale\Options\Ptranslate|array|null $options [default = null] <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @return string <p>The translation of the given plural message.</p>
	 */
	final public static function ptranslate(string $message1, string $message2, float $number, ?string $number_placeholder, ?string $context = null, $options = null) : string
	{
		//initialize
		$options = Options\Ptranslate::load($options);
		$parameters = $options->parameters;
		$message = abs($number) === 1.0 ? $message1 : $message2;
		if ($number === floor($number)) {
			$number = (int)$number;
		}
		if (isset($number_placeholder)) {
			$parameters[$number_placeholder] = $number;
		}
		
		
		//TODO: implement the actual translation
		
		
		//parameters
		$message = UText::fill($message, $parameters, [
			'info_scope' => $options->info_scope,
			'translate' => true,
			'language' => $options->language
		], [
			'string_flags' => $options->string_flags,
			'stringifier' => $options->stringifier
		]);
		
		//return
		return $message;
	}
}
