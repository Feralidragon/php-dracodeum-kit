<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Utilities\Base64\Exceptions;

/**
 * Core Base64 utility class.
 * 
 * This utility implements a set of methods used to encode and decode Base64 strings.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Base64
 */
final class Base64 extends Utility
{
	/**
	 * Encode a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to encode.</p>
	 * @param bool $url_safe [default = false] <p>Use URL-safe encoding, in which the plus signs (<code>+</code>) and slashes (<code>/</code>) get replaced 
	 * by hyphens (<code>-</code>) and underscores (<code>_</code>) respectively, as well as the padding equal signs (<code>=</code>) removed, 
	 * in order to be safely put in an URL.</p>
	 * @return string <p>The given string encoded.</p>
	 */
	final public static function encode(string $string, bool $url_safe = false) : string
	{
		return $url_safe ? strtr(rtrim(base64_encode($string), '='), '+/', '-_') : base64_encode($string);
	}
	
	/**
	 * Decode a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string <p>The string to decode.</p>
	 * @param bool|null $url_safe [default = null] <p>Use URL-safe decoding, in which the plus signs (<code>+</code>) and slashes (<code>/</code>) got replaced 
	 * by hyphens (<code>-</code>) and underscores (<code>_</code>) respectively, as well as the padding equal signs (<code>=</code>) removed, 
	 * in order to have been safely put in an URL.<br>
	 * If not set, the used encoding is automatically detected from the given string.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Base64\Exceptions\DecodeInvalidString
	 * @return string <p>The given string decoded.</p>
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
