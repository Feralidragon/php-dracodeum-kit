<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
//use Dracodeum\Kit\Utilities\Base32\Exceptions;
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
}
