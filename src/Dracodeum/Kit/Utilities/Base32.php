<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Base32\Exceptions;
use Dracodeum\Kit\Enumerations\Base32\Alphabet as EAlphabet;

/**
 * This utility implements a set of methods used to encode and decode Base32 strings.
 * 
 * @see https://en.wikipedia.org/wiki/Base32
 * @see https://tools.ietf.org/html/rfc4648
 */
final class Base32 extends Utility
{
	//Final public static methods
	/**
	 * Check if a given string is encoded.
	 *
	 * @param string $string
	 * <p>The string to check.</p>
	 * @param string $alphabet [default = \Dracodeum\Kit\Enumerations\Base32\Alphabet::RFC4648]
	 * <p>The alphabet to check with.<br>
	 * It must be exactly 32 characters long.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given string is encoded.</p>
	 */
	final public static function encoded(string $string, string $alphabet = EAlphabet::RFC4648): bool
	{
		//check
		if (strlen($alphabet) !== 32) {
			Call::haltParameter('alphabet', $alphabet, [
				'hint_message' => "The given alphabet must be exactly 32 characters long."
			]);
		}
		
		//patterns
		static $patterns = [];
		if (!isset($patterns[$alphabet])) {
			$p = preg_quote($alphabet, '/');
			$patterns[$alphabet] = "/^(?:[{$p}]{8})*" . 
				"(?:[{$p}]{2}(?:\={6})?|[{$p}]{4}(?:\={4})?|[{$p}]{5}(?:\={3})?|[{$p}]{7}\=?|[{$p}]{8})$/";
			unset($p);
		}
		
		//return
		return preg_match($patterns[$alphabet], $string);
	}
	
	/**
	 * Encode a given string.
	 * 
	 * @param string $string
	 * <p>The string to encode.</p>
	 * @param bool $url_safe [default = false]
	 * <p>Use URL-safe encoding, with the padding equal signs (<samp>=</samp>) removed, 
	 * in order to be safely put in a URL.</p>
	 * @param string $alphabet [default = \Dracodeum\Kit\Enumerations\Base32\Alphabet::RFC4648]
	 * <p>The alphabet to encode with.<br>
	 * It must be exactly 32 characters long.</p>
	 * @return string
	 * <p>The given string encoded.</p>
	 */
	final public static function encode(
		string $string, bool $url_safe = false, string $alphabet = EAlphabet::RFC4648
	): string
	{
		//check
		if (strlen($alphabet) !== 32) {
			Call::haltParameter('alphabet', $alphabet, [
				'hint_message' => "The given alphabet must be exactly 32 characters long."
			]);
		} elseif ($string === '') {
			return '';
		}
		
		//encode
		$encoded_string = '';
		foreach (str_split($string, 5) as $chunk) {
			//size
			$size = strlen($chunk);
			if ($size < 5) {
				$chunk .= str_repeat("\x00", 5 - $size);
			}
			
			//encode
			$i = $leftover = 0;
			$bytes = unpack('C5', $chunk);
			while ($i < 5) {
				//shift
				$shift_l = 0;
				$shift_r = $leftover - 5;
				if ($shift_r < 0) {
					$shift_l = -$shift_r;
					$shift_r = 8 - $shift_l;
					if ($shift_l >= 5) {
						$shift_l = 0;
					}
				}
				
				//index
				$index = 0;
				if ($shift_l > 0) {
					$index |= $bytes[$i] << $shift_l;
				}
				$index |= $shift_r > 0 ? $bytes[$i + 1] >> $shift_r : $bytes[$i + 1];
				$index &= 0x1f;
				
				//string
				$encoded_string .= $alphabet[$index];
				
				//finalize
				$leftover = $shift_r;
				if ($leftover < 5) {
					$i++;
				}
			}
			
			//padding
			if ($size < 5) {
				static $padding_map = [1 => 6, 2 => 4, 3 => 3, 4 => 1];
				$padding = $padding_map[$size];
				$encoded_string = substr($encoded_string, 0, strlen($encoded_string) - $padding);
				if (!$url_safe) {
					$encoded_string .= str_repeat('=', $padding);
				}
			}
		}
		return $encoded_string;
	}
	
	/**
	 * Decode a given string.
	 * 
	 * @param string $string
	 * <p>The string to decode.</p>
	 * @param string $alphabet [default = \Dracodeum\Kit\Enumerations\Base32\Alphabet::RFC4648]
	 * <p>The alphabet to decode with.<br>
	 * It must be exactly 32 characters long.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Base32\Exceptions\Decode\InvalidString
	 * @return string|null
	 * <p>The given string decoded.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be decoded.</p>
	 */
	final public static function decode(
		string $string, string $alphabet = EAlphabet::RFC4648, bool $no_throw = false
	): ?string
	{
		//check
		if (strlen($alphabet) !== 32) {
			Call::haltParameter('alphabet', $alphabet, [
				'hint_message' => "The given alphabet must be exactly 32 characters long."
			]);
		} elseif (!self::encoded($string, $alphabet)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Decode\InvalidString([$string, $alphabet]);
		}
		
		//decode
		//TODO
	}
	
	/**
	 * Normalize a given string.
	 * 
	 * @param string $string
	 * <p>The string to normalize.</p>
	 * @param string $alphabet_from [default = \Dracodeum\Kit\Enumerations\Base32\Alphabet::RFC4648]
	 * <p>The alphabet to normalize from.<br>
	 * It must be exactly 32 characters long.</p>
	 * @param string $alphabet_to [default = \Dracodeum\Kit\Enumerations\Base32\Alphabet::RFC4648]
	 * <p>The alphabet to normalize to.<br>
	 * It must be exactly 32 characters long.</p>
	 * @param bool $no_throw [default = false]
	 * <p>Do not throw an exception.</p>
	 * @throws \Dracodeum\Kit\Utilities\Base32\Exceptions\Normalize\InvalidString
	 * @return string|null
	 * <p>The given string normalized.<br>
	 * If <var>$no_throw</var> is set to boolean <code>true</code>, 
	 * then <code>null</code> is returned if it could not be normalized.</p>
	 */
	final public static function normalize(
		string $string, string $alphabet_from = EAlphabet::RFC4648, string $alphabet_to = EAlphabet::RFC4648,
		bool $no_throw = false
	): ?string
	{
		//check
		if (strlen($alphabet_from) !== 32) {
			Call::haltParameter('alphabet_from', $alphabet_from, [
				'hint_message' => "The given alphabet must be exactly 32 characters long."
			]);
		} elseif (strlen($alphabet_to) !== 32) {
			Call::haltParameter('alphabet_to', $alphabet_to, [
				'hint_message' => "The given alphabet must be exactly 32 characters long."
			]);
		} elseif (!self::encoded($string, $alphabet_from)) {
			if ($no_throw) {
				return null;
			}
			throw new Exceptions\Normalize\InvalidString([$string, $alphabet_from]);
		}
		
		//alphabet
		if ($alphabet_from !== $alphabet_to) {
			$string = strtr($string, $alphabet_from, $alphabet_to);
		}
		
		//padding
		$string = rtrim($string, '=');
		$padding = 8 - strlen($string) % 8;
		if ($padding > 0 && $padding < 8) {
			$string .= str_repeat('=', $padding);
		}
		
		//return
		return $string;
	}
}
