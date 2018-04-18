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
	 * Encode a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to encode.</p>
	 * @param bool $url_safe [default = false]
	 * <p>Use URL-safe encoding, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) get replaced 
	 * by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
	 * as well as the padding equal signs (<samp>=</samp>) removed, in order to be safely put in an URL.</p>
	 * @return string
	 * <p>The given string encoded.</p>
	 */
	final public static function encode(string $string, bool $url_safe = false) : string
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
	 * <p>Use URL-safe decoding, in which the plus signs (<samp>+</samp>) and slashes (<samp>/</samp>) got replaced 
	 * by hyphens (<samp>-</samp>) and underscores (<samp>_</samp>) respectively, 
	 * as well as the padding equal signs (<samp>=</samp>) removed, in order to have been safely put in an URL.<br>
	 * If not set, then the used encoding is automatically detected from the given string.</p>
	 * @throws \Feralygon\Kit\Utilities\Base64\Exceptions\DecodeInvalidString
	 * @return string
	 * <p>The given string decoded.</p>
	 */
	final public static function decode(string $string, ?bool $url_safe = null) : string
	{
		if (!isset($url_safe)) {
			$url_safe = (bool)preg_match('/[_\-]/', $string);
		}
		if (!preg_match($url_safe ? '/^[\w\-]+$/' : '/^[a-z\d+\/]+\=*$/i', $string)) {
			throw new Exceptions\DecodeInvalidString(['string' => $string, 'url_safe' => $url_safe]);
		}
		return $url_safe ? base64_decode(strtr($string, '-_', '+/')) : base64_decode($string);
	}
}
