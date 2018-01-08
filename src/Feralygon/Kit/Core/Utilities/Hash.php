<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Utilities\Hash\Exceptions;

/**
 * Core hash utility class.
 * 
 * This utility implements a set of methods used to evaluate and coerce hash values.
 * 
 * @since 1.0.0
 */
final class Hash extends Utility
{
	//Final public static methods
	/**
	 * Evaluate a given value as a hash.
	 * 
	 * Only the following types and formats can be evaluated into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to evaluate (validate and sanitize).</p>
	 * @param int $bits <p>The number of bits to evaluate with.<br>
	 * It must be a multiple of <code>8</code> and be greater than <code>0</code>.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Hash\Exceptions\InvalidBits
	 * @return bool <p>Boolean <samp>true</samp> if the given value is successfully evaluated into a hash.</p>
	 */
	final public static function evaluate(&$value, int $bits, bool $nullable = false) : bool
	{
		//validate
		if ($bits <= 0 || $bits % 8 !== 0) {
			throw new Exceptions\InvalidBits(['bits' => $bits]);
		}
		
		//evaluate
		if (!isset($value)) {
			return $nullable;
		} elseif (!is_string($value)) {
			return false;
		} elseif (strlen($value) === $bits / 4 && preg_match('/^[\da-f]+$/i', $value)) {
			$value = strtolower($value);
			return true;
		} elseif (strlen(rtrim($value, '=')) === (int)ceil($bits / 6) && preg_match('/^[\w\-+\/]+\={0,2}$/', $value)) {
			$value = bin2hex(Base64::decode($value));
			return true;
		} elseif (strlen($value) === $bits / 8) {
			$value = bin2hex($value);
			return true;
		}
		return false;
	}
	
	/**
	 * Coerce a given value into a hash.
	 * 
	 * Only the following types and formats can be coerced into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or an URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to coerce (validate and sanitize).</p>
	 * @param int $bits <p>The number of bits to coerce with.<br>
	 * It must be a multiple of <code>8</code> and be greater than <code>0</code>.</p>
	 * @param bool $nullable [default = false] <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Hash\Exceptions\InvalidBits
	 * @throws \Feralygon\Kit\Core\Utilities\Hash\Exceptions\CoercionFailed
	 * @return string|null <p>The given value coerced into a hash.<br>
	 * If nullable, <samp>null</samp> may also be returned.</p>
	 */
	final public static function coerce($value, int $bits, bool $nullable = false) : ?string
	{
		//validate
		if ($bits <= 0 || $bits % 8 !== 0) {
			throw new Exceptions\InvalidBits(['bits' => $bits]);
		}
		
		//evaluate
		if (!self::evaluate($value, $bits, $nullable)) {
			throw new Exceptions\CoercionFailed(['value' => $value, 'bits' => $bits]);
		}
		return $value;
	}
}
