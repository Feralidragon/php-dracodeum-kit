<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities;

use Feralygon\Kit\Core\Utility;
use Feralygon\Kit\Core\Utilities\Json\{
	Options,
	Exceptions
};

/**
 * Core JSON utility class.
 * 
 * This utility implements a set of methods used to encode and decode JSON (JavaScript Object Notation) data.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/JSON
 */
final class Json extends Utility
{
	/**
	 * Encode data.
	 * 
	 * @since 1.0.0
	 * @param mixed $data <p>The data to encode.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Json\Options\Encode|array|null $options [default = null] 
	 * <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Json\Exceptions\EncodeInvalidData
	 * @return string <p>The given data encoded.</p>
	 */
	final public static function encode($data, $options = null) : string
	{
		$options = Options\Encode::coerce($options);
		$encoded_data = isset($options->depth)
			? json_encode($data, $options->flags, $options->depth)
			: json_encode($data, $options->flags);
		$error_code = json_last_error();
		if ($error_code !== JSON_ERROR_NONE) {
			throw new Exceptions\EncodeInvalidData([
				'data' => $data,
				'error_code' => $error_code,
				'error_message' => json_last_error_msg()
			]);
		}
		return $encoded_data;
	}
	
	/**
	 * Decode data.
	 * 
	 * @since 1.0.0
	 * @param string $data <p>The data to decode.</p>
	 * @param \Feralygon\Kit\Core\Utilities\Json\Options\Decode|array|null $options [default = null] 
	 * <p>Additional options, as an instance or <samp>name => value</samp> pairs.</p>
	 * @throws \Feralygon\Kit\Core\Utilities\Json\Exceptions\DecodeInvalidData
	 * @return mixed <p>The given data decoded.</p>
	 */
	final public static function decode(string $data, $options = null)
	{
		$options = Options\Decode::coerce($options);
		$decoded_data = isset($options->depth)
			? json_decode($data, $options->associative, $options->depth, $options->flags)
			: json_decode($data, $options->associative);
		$error_code = json_last_error();
		if ($error_code !== JSON_ERROR_NONE) {
			throw new Exceptions\DecodeInvalidData([
				'data' => $data,
				'error_code' => $error_code,
				'error_message' => json_last_error_msg()
			]);
		}
		return $decoded_data;
	}
}
