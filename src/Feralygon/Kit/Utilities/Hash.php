<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\Utility;
use Feralygon\Kit\Utilities\Hash\Exceptions;

/**
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
	 * @return bool <p>Boolean <code>true</code> if the given value is successfully evaluated into a hash.</p>
	 */
	final public static function evaluate(&$value, int $bits, bool $nullable = false) : bool
	{
		try {
			$value = self::coerce($value, $bits, $nullable);
		} catch (Exceptions\CoercionFailed $exception) {
			return false;
		}
		return true;
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
	 * @throws \Feralygon\Kit\Utilities\Hash\Exceptions\InvalidBits
	 * @throws \Feralygon\Kit\Utilities\Hash\Exceptions\CoercionFailed
	 * @return string|null <p>The given value coerced into a hash.<br>
	 * If nullable, <code>null</code> may also be returned.</p>
	 */
	final public static function coerce($value, int $bits, bool $nullable = false) : ?string
	{
		//validate
		if ($bits <= 0 || $bits % 8 !== 0) {
			throw new Exceptions\InvalidBits(['bits' => $bits]);
		}
		
		//coerce
		if (!isset($value)) {
			if ($nullable) {
				return null;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_string($value)) {
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a hash value given as a string is allowed."
			]);
		} elseif (strlen($value) === $bits / 4 && preg_match('/^[\da-f]+$/i', $value)) {
			return strtolower($value);
		} elseif (strlen(rtrim($value, '=')) === (int)ceil($bits / 6) && preg_match('/^[\w\-+\/]+\={0,2}$/', $value)) {
			return bin2hex(Base64::decode($value));
		} elseif (strlen($value) === $bits / 8) {
			return bin2hex($value);
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID,
			'error_message' => Text::pfill(
				"Only a hash value of {{bits}} bit is allowed, " . 
					"for which only the following types and formats can be coerced into such:\n" . 
					" - a hexadecimal notation string;\n" . 
					" - a Base64 or an URL-safe Base64 encoded string;\n" . 
					" - a raw binary string.",
				"Only a hash value of {{bits}} bits is allowed, " . 
					"for which only the following types and formats can be coerced into such:\n" . 
					" - a hexadecimal notation string;\n" . 
					" - a Base64 or an URL-safe Base64 encoded string;\n" . 
					" - a raw binary string.",
				$bits, 'bits'
			)
		]);
	}
}
