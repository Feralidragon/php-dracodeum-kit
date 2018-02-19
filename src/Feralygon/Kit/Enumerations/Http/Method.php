<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Http;

use Feralygon\Kit\Enumeration;
use Feralygon\Kit\Utilities\Text as UText;
use Feralygon\Kit\Options\Text as TextOptions;

/**
 * HTTP method enumeration class.
 * 
 * This enumeration represents HTTP request methods.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
 */
class Method extends Enumeration
{
	//Public constants
	/** Retrieve a full HTTP resource (both headers and content). */
	public const GET = 'GET';
	
	/** Retrieve the head of an HTTP resource (headers only). */
	public const HEAD = 'HEAD';
	
	/** Create a new HTTP resource. */
	public const POST = 'POST';
	
	/** Update an existing HTTP resource fully. */
	public const PUT = 'PUT';
	
	/** Update an existing HTTP resource partially. */
	public const PATCH = 'PATCH';
	
	/** Eliminate an existing HTTP resource permanently. */
	public const DELETE = 'DELETE';
	
	/** Retrieve back the current request exactly as it arrived to the remote web server. */
	public const TRACE = 'TRACE';
	
	/** Retrieve the allowed HTTP methods of an HTTP resource. */
	public const OPTIONS = 'OPTIONS';
	
	/** Open a TCP/IP tunnel for bidireccional communication with an HTTP resource through a proxy. */
	public const CONNECT = 'CONNECT';
	
	
	
	//Implemented protected static methods (enumeration information trait)
	/** {@inheritdoc} */
	protected static function retrieveLabel(string $name, TextOptions $text_options) : ?string
	{
		return $name;
	}
	
	/** {@inheritdoc} */
	protected static function retrieveDescription(string $name, TextOptions $text_options) : ?string
	{
		switch ($name) {
			case 'GET':
				return UText::localize(
					"Retrieve a full HTTP resource (both headers and content).",
					self::class, $text_options
				);
			case 'HEAD':
				return UText::localize(
					"Retrieve the head of an HTTP resource (headers only).",
					self::class, $text_options
				);
			case 'POST':
				return UText::localize(
					"Create a new HTTP resource.",
					self::class, $text_options
				);
			case 'PUT':
				return UText::localize(
					"Update an existing HTTP resource fully.",
					self::class, $text_options
				);
			case 'PATCH':
				return UText::localize(
					"Update an existing HTTP resource partially.",
					self::class, $text_options
				);
			case 'DELETE':
				return UText::localize(
					"Eliminate an existing HTTP resource permanently.",
					self::class, $text_options
				);
			case 'TRACE':
				return UText::localize(
					"Retrieve back the current request exactly as it arrived to the remote web server.",
					self::class, $text_options
				);
			case 'OPTIONS':
				return UText::localize(
					"Retrieve the allowed HTTP methods of an HTTP resource.",
					self::class, $text_options
				);
			case 'CONNECT':
				return UText::localize(
					"Open a TCP/IP tunnel for bidireccional communication with an HTTP resource through a proxy.",
					self::class, $text_options
				);
		}
		return null;
	}
}
