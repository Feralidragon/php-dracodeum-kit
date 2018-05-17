<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities;

use Feralygon\Kit\Utility;
use Feralygon\Kit\Utilities\Json\{
	Options,
	Exceptions
};

/**
 * This utility implements a set of methods used to encode and decode JSON (JavaScript Object Notation) data.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/JSON
 */
final class Json extends Utility
{
	//Final public static methods
	/**
	 * Check if given data is encoded.
	 * 
	 * @since 1.0.0
	 * @param string $data
	 * <p>The data to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given data is encoded.</p>
	 */
	final public static function isEncoded(string $data) : bool
	{
		json_decode($data);
		return json_last_error() === JSON_ERROR_NONE;
	}
	
	/**
	 * Encode data.
	 * 
	 * @since 1.0.0
	 * @param mixed $data
	 * <p>The data to encode.</p>
	 * @param \Feralygon\Kit\Utilities\Json\Options\Encode|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Json\Exceptions\Encode\InvalidData
	 * @return string|null
	 * <p>The given data encoded.<br>
	 * If <var>$options->no_throw</var> is set to <code>true</code>, 
	 * then <code>null</code> is returned if it could not be encoded.</p>
	 */
	final public static function encode($data, $options = null) : ?string
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
				'data' => $data, 'error_code' => $error_code, 'error_message' => json_last_error_msg()
			]);
		}
		
		//return
		return $encoded_data;
	}
	
	/**
	 * Decode data.
	 * 
	 * @since 1.0.0
	 * @param string $data
	 * <p>The data to decode.</p>
	 * @param \Feralygon\Kit\Utilities\Json\Options\Decode|array|null $options [default = null]
	 * <p>Additional options to use, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Utilities\Json\Exceptions\Decode\InvalidData
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
				'data' => $data, 'error_code' => $error_code, 'error_message' => json_last_error_msg()
			]);
		}
		
		//return
		return $decoded_data;
	}
}
