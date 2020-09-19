<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Http;

use Dracodeum\Kit\Enumeration;
use Dracodeum\Kit\Utilities\Text as UText;
use Dracodeum\Kit\Options\Text as TextOptions;

/**
 * This enumeration represents HTTP request methods.
 * 
 * @see https://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods
 */
class Method extends Enumeration
{
	//Public constants
	/** Get the head of an HTTP resource (headers only). */
	public const HEAD = 'HEAD';
	
	/** Get a full HTTP resource (both headers and content). */
	public const GET = 'GET';
	
	/** Create a new HTTP resource. */
	public const POST = 'POST';
	
	/** Update an existing HTTP resource fully. */
	public const PUT = 'PUT';
	
	/** Update an existing HTTP resource partially. */
	public const PATCH = 'PATCH';
	
	/** Eliminate an existing HTTP resource permanently. */
	public const DELETE = 'DELETE';
	
	/** Get back the current request exactly as it arrived to the remote web server. */
	public const TRACE = 'TRACE';
	
	/** Get the allowed HTTP methods of an HTTP resource. */
	public const OPTIONS = 'OPTIONS';
	
	/** Open a TCP/IP tunnel for bidireccional communication with an HTTP resource through a proxy. */
	public const CONNECT = 'CONNECT';
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Enumeration\Traits\Information)
	/** {@inheritdoc} */
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		return $name;
	}
	
	/** {@inheritdoc} */
	protected static function returnDescription(string $name, TextOptions $text_options): ?string
	{
		switch ($name) {
			case 'HEAD':
				/** @description "HEAD" description. */
				return UText::localize(
					"Get the head of an HTTP resource (headers only).",
					self::class, $text_options
				);
			case 'GET':
				/** @description "GET" description. */
				return UText::localize(
					"Get a full HTTP resource (both headers and content).",
					self::class, $text_options
				);
			case 'POST':
				/** @description "POST" description. */
				return UText::localize(
					"Create a new HTTP resource.",
					self::class, $text_options
				);
			case 'PUT':
				/** @description "PUT" description. */
				return UText::localize(
					"Update an existing HTTP resource fully.",
					self::class, $text_options
				);
			case 'PATCH':
				/** @description "PATCH" description. */
				return UText::localize(
					"Update an existing HTTP resource partially.",
					self::class, $text_options
				);
			case 'DELETE':
				/** @description "DELETE" description. */
				return UText::localize(
					"Eliminate an existing HTTP resource permanently.",
					self::class, $text_options
				);
			case 'TRACE':
				/** @description "TRACE" description. */
				return UText::localize(
					"Get back the current request exactly as it arrived to the remote web server.",
					self::class, $text_options
				);
			case 'OPTIONS':
				/** @description "OPTIONS" description. */
				return UText::localize(
					"Get the allowed HTTP methods of an HTTP resource.",
					self::class, $text_options
				);
			case 'CONNECT':
				/** @description "CONNECT" description. */
				return UText::localize(
					"Open a TCP/IP tunnel for bidireccional communication with an HTTP resource through a proxy.",
					self::class, $text_options
				);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Check if a given element represents an HTTP method which has a request body.
	 * 
	 * @param string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents an HTTP method which has a request body.</p>
	 */
	public static function hasRequestBody(string $element): bool
	{
		return in_array(static::getValue($element), [self::POST, self::PUT, self::PATCH], true);
	}
	
	/**
	 * Check if a given element represents an HTTP method which has a response body.
	 * 
	 * @param string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents an HTTP method which has a response body.</p>
	 */
	public static function hasResponseBody(string $element): bool
	{
		return in_array(static::getValue($element), [
			self::GET, self::POST, self::PUT, self::PATCH, self::DELETE, self::TRACE, self::CONNECT, self::OPTIONS
		], true);
	}
	
	/**
	 * Check if a given element represents a cacheable HTTP method.
	 * 
	 * @param string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a cacheable HTTP method.</p>
	 */
	public static function isCacheable(string $element): bool
	{
		return in_array(static::getValue($element), [self::HEAD, self::GET], true);
	}
	
	/**
	 * Check if a given element represents a nullipotent HTTP method.
	 * 
	 * @param string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a nullipotent HTTP method.</p>
	 */
	public static function isNullipotent(string $element): bool
	{
		return in_array(static::getValue($element), [self::HEAD, self::GET, self::OPTIONS], true);
	}
	
	/**
	 * Check if a given element represents an idempotent HTTP method.
	 * 
	 * @param string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents an idempotent HTTP method.</p>
	 */
	public static function isIdempotent(string $element): bool
	{
		return in_array(static::getValue($element), [
			self::HEAD, self::GET, self::PUT, self::DELETE, self::OPTIONS
		], true);
	}
}
