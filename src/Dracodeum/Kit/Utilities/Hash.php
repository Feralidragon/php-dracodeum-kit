<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Hash\Exceptions;

/** This utility implements a set of methods used to evaluate and coerce hash values. */
final class Hash extends Utility
{
	//Final public static methods
	/**
	 * Colonify a given hash.
	 * 
	 * The process of colonification of a given hash consists in splitting it into small groups of hexadecimal digits, 
	 * using the colon character (<samp>:</samp>) as a delimiter.<br>
	 * By omission, the hash is colonified into hexadecimal octets.
	 * 
	 * @param string $hash
	 * <p>The hash to colonify.</p>
	 * @param bool $hextets [default = false]
	 * <p>Colonify the given hash into hextets.</p>
	 * @return string
	 * <p>The colonified hash from the given one.</p>
	 */
	final public static function colonify(string $hash, bool $hextets = false): string
	{
		$hash = self::coerce($hash);
		Call::guardParameter('hash', $hash, !$hextets || strlen($hash) % 4 === 0, [
			'error_message' => "The given hash is missing an octet in order to be colonified into hextets."
		]);
		preg_match_all('/[\da-f]{' . ($hextets ? 4 : 2) . '}/i', $hash, $matches);
		return implode(':', $matches[0]);
	}
	
	/**
	 * Evaluate a given value as a hash.
	 * 
	 * Only the following types and formats can be evaluated into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or a URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to evaluate (validate and sanitize).</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to evaluate with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to evaluate as <code>null</code>.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully evaluated into a hash.</p>
	 */
	final public static function evaluate(&$value, ?int $bits = null, bool $nullable = false): bool
	{
		return self::processCoercion($value, $bits, $nullable, true);
	}
	
	/**
	 * Coerce a given value into a hash.
	 * 
	 * Only the following types and formats can be coerced into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or a URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @param mixed $value
	 * <p>The value to coerce (validate and sanitize).</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to coerce with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @throws \Dracodeum\Kit\Utilities\Hash\Exceptions\CoercionFailed
	 * @return string|null
	 * <p>The given value coerced into a hash.<br>
	 * If nullable, then <code>null</code> may also be returned.</p>
	 */
	final public static function coerce($value, ?int $bits = null, bool $nullable = false): ?string
	{
		self::processCoercion($value, $bits, $nullable);
		return $value;
	}
	
	/**
	 * Process the coercion of a given value into a hash.
	 * 
	 * Only the following types and formats can be coerced into a hash:<br>
	 * &nbsp; &#8226; &nbsp; a hexadecimal notation string;<br>
	 * &nbsp; &#8226; &nbsp; a colon-hexadecimal notation string, as octets or hextets;<br>
	 * &nbsp; &#8226; &nbsp; a Base64 or a URL-safe Base64 encoded string;<br>
	 * &nbsp; &#8226; &nbsp; a raw binary string.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process (validate and sanitize).</p>
	 * @param int|null $bits [default = null]
	 * <p>The number of bits to coerce with.</p>
	 * @param bool $nullable [default = false]
	 * <p>Allow the given value to coerce as <code>null</code>.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Hash\Exceptions\CoercionFailed
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully coerced into a hash.</p>
	 */
	final public static function processCoercion(
		&$value, ?int $bits = null, bool $nullable = false, bool $no_throw = false
	): bool
	{
		//guard
		Call::guardParameter('bits', $bits, !isset($bits) || ($bits > 0 && $bits % 8 === 0), [
			'hint_message' => "Only null or a multiple of 8 and a value greater than 0 is allowed."
		]);
		
		//check
		if (!isset($value)) {
			if ($nullable) {
				return true;
			} elseif ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_NULL,
				'error_message' => "A null value is not allowed."
			]);
		} elseif (!is_string($value)) {
			if ($no_throw) {
				return false;
			}
			throw new Exceptions\CoercionFailed([
				'value' => $value,
				'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID_TYPE,
				'error_message' => "Only a hash value given as a string is allowed."
			]);
		}
		
		//length
		$length = strlen($value);
		
		//hexadecimal
		if (preg_match('/^([\da-f]{2})+$/i', $value) && (!isset($bits) || $length === $bits / 4)) {
			$value = strtolower($value);
			return true;
		}
		
		//colon-hexadecimal
		foreach ([2, 4] as $n) {
			$n_length = isset($bits) ? ($n + 1) * $bits / (4 * $n) - 1 : null;
			if (
				preg_match("/^[\da-f]{{$n}}(?::[\da-f]{{$n}})*$/i", $value) && 
				(!isset($n_length) || $length === $n_length)
			) {
				$value = strtolower(str_replace(':', '', $value));
				return true;
			}
		}
		
		//base64
		if (
			preg_match('/^[\w\-+\/]+\={0,2}$/', $value) && 
			(!isset($bits) || strlen(rtrim($value, '=')) === (int)ceil($bits / 6))
		) {
			$hash = Base64::decode($value, null, true);
			if (isset($hash)) {
				$value = bin2hex($hash);
				return true;
			}
		}
		
		//binary
		if (!isset($bits) || $length === $bits / 8) {
			$value = bin2hex($value);
			return true;
		}
		
		//finalize
		if ($no_throw) {
			return false;
		}
		throw new Exceptions\CoercionFailed([
			'value' => $value,
			'error_code' => Exceptions\CoercionFailed::ERROR_CODE_INVALID,
			'error_message' => Text::fill(
				"Only a hash value of {{bits}} bits is allowed, " . 
				"for which only the following types and formats can be coerced into such:\n" . 
				" - a hexadecimal notation string;\n" . 
				" - a colon-hexadecimal notation string, as octets or hextets;\n" . 
				" - a Base64 or a URL-safe Base64 encoded string;\n" . 
				" - a raw binary string.",
				['bits' => $bits]
			)
		]);
	}
}
