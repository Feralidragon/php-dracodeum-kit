<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities;

use Dracodeum\Kit\Utility;
use Dracodeum\Kit\Utilities\Json\{
	Options,
	Exceptions
};

/**
 * This utility implements a set of methods used to encode and decode JSON (JavaScript Object Notation) data.
 * 
 * @see https://en.wikipedia.org/wiki/JSON
 */
final class Json extends Utility
{
	//Final public static methods
	/**
	 * Check if given data is encoded.
	 * 
	 * @param string $data
	 * <p>The data to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given data is encoded.</p>
	 */
	final public static function encoded(string $data): bool
	{
		json_decode($data);
		return json_last_error() === JSON_ERROR_NONE;
	}
	
	/**
	 * Encode given data.
	 * 
	 * @param mixed $data
	 * <p>The data to encode.</p>
	 * @param \Dracodeum\Kit\Utilities\Json\Options\Encode|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Json\Exceptions\Encode\InvalidData
	 * @return string|null
	 * <p>The given data encoded.<br>
	 * If <var>$options->no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be encoded.</p>
	 */
	final public static function encode($data, $options = null): ?string
	{
		//encode
		$options = Options\Encode::coerce($options);
		$encoded_data = isset($options->depth)
			? json_encode($data, $options->flags, $options->depth)
			: json_encode($data, $options->flags);
		
		//error
		$error_code = json_last_error();
		if ($error_code !== JSON_ERROR_NONE) {
			if ($options->no_throw) {
				return null;
			}
			throw new Exceptions\Encode\InvalidData([
				$data, 'error_code' => $error_code, 'error_message' => json_last_error_msg()
			]);
		}
		
		//return
		return $encoded_data;
	}
	
	/**
	 * Decode given data.
	 * 
	 * @param string $data
	 * <p>The data to decode.</p>
	 * @param \Dracodeum\Kit\Utilities\Json\Options\Decode|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Dracodeum\Kit\Utilities\Json\Exceptions\Decode\InvalidData
	 * @return mixed
	 * <p>The given data decoded.<br>
	 * If <var>$options->no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> may also be returned if it could not be decoded.</p>
	 */
	final public static function decode(string $data, $options = null)
	{
		//decode
		$options = Options\Decode::coerce($options);
		$decoded_data = isset($options->depth)
			? json_decode($data, $options->associative, $options->depth, $options->flags)
			: json_decode($data, $options->associative);
		
		//error
		$error_code = json_last_error();
		if ($error_code !== JSON_ERROR_NONE) {
			if ($options->no_throw) {
				return null;
			}
			throw new Exceptions\Decode\InvalidData([
				$data, 'error_code' => $error_code, 'error_message' => json_last_error_msg()
			]);
		}
		
		//return
		return $decoded_data;
	}
}
