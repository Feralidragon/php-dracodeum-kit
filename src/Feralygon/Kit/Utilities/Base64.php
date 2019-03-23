<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\Utility;
use Feralygon\Kit\Utilities\Base64\Exceptions;

/**
 * This utility implements a set of methods used to encode and decode Base64 strings.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Base64
 */
final class Base64 extends Utility
{
	//Final public static methods
	/**
	 * Check if a given string is encoded.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param bool|null $url_safe [default = null]
	 * <p>Check URL-safe encoding, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) are replaced 
	 * by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
	 * with the padding equal signs (<samp>=</samp>) removed, in order to be safely put in an URL.<br>
	 * If not set, then the used encoding is automatically detected from the given string.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is encoded.</p>
	 */
	final public static function encoded(string $string, ?bool $url_safe = null): bool
	{
		//url-safe
		if (!isset($url_safe)) {
			$url_safe = (bool)preg_match('/[_\-]/', $string);
		}
		
		//pattern
		$pattern = $url_safe
			? '/^(?:[\w\-]{4})*[\w\-]{2,4}$/'
			: '/^(?:[a-z\d+\/]{4})*(?:[a-z\d+\/]{2}(?:\={2})?|[a-z\d+\/]{3}\=?|[a-z\d+\/]{4})$/i';
		
		//return
		return preg_match($pattern, $string);
	}
	
	/**
	 * Encode a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to encode.</p>
	 * @param bool $url_safe [default = false]
	 * <p>Use URL-safe encoding, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) are replaced 
	 * by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
	 * with the padding equal signs (<samp>=</samp>) removed, in order to be safely put in an URL.</p>
	 * @return string
	 * <p>The given string encoded.</p>
	 */
	final public static function encode(string $string, bool $url_safe = false): string
	{
		return $url_safe ? strtr(rtrim(base64_encode($string), '='), '+/', '-_') : base64_encode($string);
	}
	
	/**
	 * Decode a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to decode.</p>
	 * @param bool|null $url_safe [default = null]
	 * <p>Use URL-safe decoding, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) are replaced 
	 * by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
	 * with the padding equal signs (<samp>=</samp>) removed, in order to be safely put in an URL.<br>
	 * If not set, then the used encoding is automatically detected from the given string.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Base64\Exceptions\Decode\InvalidString
	 * @return string|null
	 * <p>The given string decoded.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be decoded.</p>
	 */
	final public static function decode(string $string, ?bool $url_safe = null, bool $no_throw = false): ?string
	{
		if (!self::encoded($string, $url_safe)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Decode\InvalidString([$string, 'url_safe' => $url_safe ?? false]);
		}
		return base64_decode($url_safe === false ? $string : self::normalize($string));
	}
	
	/**
	 * Normalize a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to normalize.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Feralygon\Kit\Utilities\Base64\Exceptions\Normalize\InvalidString
	 * @return string|null
	 * <p>The given string normalized.<br>
	 * If <var>$no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be normalized.</p>
	 */
	final public static function normalize(string $string, bool $no_throw = false): ?string
	{
		//check
		if (!self::encoded($string)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Normalize\InvalidString([$string]);
		}
		
		//normalize
		$string = rtrim(strtr($string, '-_', '+/'), '=');
		$padding = 4 - strlen($string) % 4;
		if ($padding > 0 && $padding < 3) {
			$string .= str_repeat('=', $padding);
		}
		return $string;
	}
}
