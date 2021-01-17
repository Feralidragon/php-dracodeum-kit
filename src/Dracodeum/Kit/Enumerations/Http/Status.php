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
 * This enumeration represents HTTP response status codes.
 * 
 * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class Status extends Enumeration
{
	//Public constants
	/** Informational "Continue". */
	public const CONTINUE = 100;
	
	/** Informational "Switching Protocols". */
	public const SWITCHING_PROTOCOLS = 101;
	
	/** Informational "Processing". */
	public const PROCESSING = 102;
	
	/** Success "OK". */
	public const OK = 200;
	
	/** Success "Created". */
	public const CREATED = 201;
	
	/** Success "Accepted". */
	public const ACCEPTED = 202;
	
	/** Success "Non-Authoritative Information". */
	public const NON_AUTHORITATIVE_INFORMATION = 203;
	
	/** Success "No Content". */
	public const NO_CONTENT = 204;
	
	/** Success "Reset Content". */
	public const RESET_CONTENT = 205;
	
	/** Success "Partial Content". */
	public const PARTIAL_CONTENT = 206;
	
	/** Success "Multi-Status". */
	public const MULTI_STATUS = 207;
	
	/** Success "Already Reported". */
	public const ALREADY_REPORTED = 208;
	
	/** Success "IM Used". */
	public const IM_USED = 226;
	
	/** Redirection "Multiple Choices". */
	public const MULTIPLE_CHOICES = 300;
	
	/** Redirection "Moved Permanently". */
	public const MOVED_PERMANENTLY = 301;
	
	/** Redirection "Found". */
	public const FOUND = 302;
	
	/** Redirection "See Other". */
	public const SEE_OTHER = 303;
	
	/** Redirection "Not Modified". */
	public const NOT_MODIFIED = 304;
	
	/** Redirection "Use Proxy". */
	public const USE_PROXY = 305;
	
	/** Redirection "Switch Proxy". */
	public const SWITCH_PROXY = 306;
	
	/** Redirection "Temporary Redirect". */
	public const TEMPORARY_REDIRECT = 307;
	
	/** Redirection "Permanent Redirect". */
	public const PERMANENT_REDIRECT = 308;
	
	/** Client error "Bad Request". */
	public const BAD_REQUEST = 400;
	
	/** Client error "Unauthorized". */
	public const UNAUTHORIZED = 401;
	
	/** Client error "Payment Required". */
	public const PAYMENT_REQUIRED = 402;
	
	/** Client error "Forbidden". */
	public const FORBIDDEN = 403;
	
	/** Client error "Not Found". */
	public const NOT_FOUND = 404;
	
	/** Client error "Method Not Allowed". */
	public const METHOD_NOT_ALLOWED = 405;
	
	/** Client error "Not Acceptable". */
	public const NOT_ACCEPTABLE = 406;
	
	/** Client error "Proxy Authentication Required". */
	public const PROXY_AUTHENTICATION_REQUIRED = 407;
	
	/** Client error "Request Timeout". */
	public const REQUEST_TIMEOUT = 408;
	
	/** Client error "Conflict". */
	public const CONFLICT = 409;
	
	/** Client error "Gone". */
	public const GONE = 410;
	
	/** Client error "Length Required". */
	public const LENGTH_REQUIRED = 411;
	
	/** Client error "Precondition Failed". */
	public const PRECONDITION_FAILED = 412;
	
	/** Client error "Payload Too Large". */
	public const PAYLOAD_TOO_LARGE = 413;
	
	/** Client error "URI Too Long". */
	public const URI_TOO_LONG = 414;
	
	/** Client error "Unsupported Media Type". */
	public const UNSUPPORTED_MEDIA_TYPE = 415;
	
	/** Client error "Range Not Satisfiable". */
	public const RANGE_NOT_SATISFIABLE = 416;
	
	/** Client error "Expectation Failed". */
	public const EXPECTATION_FAILED = 417;
	
	/** Client error "I'm a teapot". */
	public const IM_A_TEAPOT = 418;
	
	/** Client error "Misdirected Request". */
	public const MISDIRECTED_REQUEST = 421;
	
	/** Client error "Unprocessable Entity". */
	public const UNPROCESSABLE_ENTITY = 422;
	
	/** Client error "Locked". */
	public const LOCKED = 423;
	
	/** Client error "Failed Dependency". */
	public const FAILED_DEPENDENCY = 424;
	
	/** Client error "Upgrade Required". */
	public const UPGRADE_REQUIRED = 426;
	
	/** Client error "Precondition Required". */
	public const PRECONDITION_REQUIRED = 428;
	
	/** Client error "Too Many Requests". */
	public const TOO_MANY_REQUESTS = 429;
	
	/** Client error "Request Header Fields Too Large". */
	public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	
	/** Client error "Unavailable For Legal Reasons". */
	public const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	
	/** Server error "Internal Server Error". */
	public const INTERNAL_SERVER_ERROR = 500;
	
	/** Server error "Not Implemented". */
	public const NOT_IMPLEMENTED = 501;
	
	/** Server error "Bad Gateway". */
	public const BAD_GATEWAY = 502;
	
	/** Server error "Service Unavailable". */
	public const SERVICE_UNAVAILABLE = 503;
	
	/** Server error "Gateway Timeout". */
	public const GATEWAY_TIMEOUT = 504;
	
	/** Server error "HTTP Version Not Supported". */
	public const HTTP_VERSION_NOT_SUPPORTED = 505;
	
	/** Server error "Variant Also Negotiates". */
	public const VARIANT_ALSO_NEGOTIATES = 506;
	
	/** Server error "Insufficient Storage". */
	public const INSUFFICIENT_STORAGE = 507;
	
	/** Server error "Loop Detected". */
	public const LOOP_DETECTED = 508;
	
	/** Server error "Not Extended". */
	public const NOT_EXTENDED = 510;
	
	/** Server error "Network Authentication Required". */
	public const NETWORK_AUTHENTICATION_REQUIRED = 511;
	
	
	
	//Implemented protected static methods (Dracodeum\Kit\Enumeration\Traits\Information)
	/** {@inheritdoc} */
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		switch ($name) {
			//informational (1xx)
			case 'CONTINUE':
				/** @description "CONTINUE" label. */
				return UText::localize("Continue", self::class, $text_options);
			case 'SWITCHING_PROTOCOLS':
				/** @description "SWITCHING_PROTOCOLS" label. */
				return UText::localize("Switching Protocols", self::class, $text_options);
			case 'PROCESSING':
				/** @description "PROCESSING" label. */
				return UText::localize("Processing", self::class, $text_options);
				
			//success (2xx)
			case 'OK':
				/** @description "OK" label. */
				return UText::localize("OK", self::class, $text_options);
			case 'CREATED':
				/** @description "CREATED" label. */
				return UText::localize("Created", self::class, $text_options);
			case 'ACCEPTED':
				/** @description "ACCEPTED" label. */
				return UText::localize("Accepted", self::class, $text_options);
			case 'NON_AUTHORITATIVE_INFORMATION':
				/** @description "NON_AUTHORITATIVE_INFORMATION" label. */
				return UText::localize("Non-Authoritative Information", self::class, $text_options);
			case 'NO_CONTENT':
				/** @description "NO_CONTENT" label. */
				return UText::localize("No Content", self::class, $text_options);
			case 'RESET_CONTENT':
				/** @description "RESET_CONTENT" label. */
				return UText::localize("Reset Content", self::class, $text_options);
			case 'PARTIAL_CONTENT':
				/** @description "PARTIAL_CONTENT" label. */
				return UText::localize("Partial Content", self::class, $text_options);
			case 'MULTI_STATUS':
				/** @description "MULTI_STATUS" label. */
				return UText::localize("Multi-Status", self::class, $text_options);
			case 'ALREADY_REPORTED':
				/** @description "ALREADY_REPORTED" label. */
				return UText::localize("Already Reported", self::class, $text_options);
			case 'IM_USED':
				/** @description "IM_USED" label. */
				return UText::localize("IM Used", self::class, $text_options);
				
			//redirection (3xx)
			case 'MULTIPLE_CHOICES':
				/** @description "MULTIPLE_CHOICES" label. */
				return UText::localize("Multiple Choices", self::class, $text_options);
			case 'MOVED_PERMANENTLY':
				/** @description "MOVED_PERMANENTLY" label. */
				return UText::localize("Moved Permanently", self::class, $text_options);
			case 'FOUND':
				/** @description "FOUND" label. */
				return UText::localize("Found", self::class, $text_options);
			case 'SEE_OTHER':
				/** @description "SEE_OTHER" label. */
				return UText::localize("See Other", self::class, $text_options);
			case 'NOT_MODIFIED':
				/** @description "NOT_MODIFIED" label. */
				return UText::localize("Not Modified", self::class, $text_options);
			case 'USE_PROXY':
				/** @description "USE_PROXY" label. */
				return UText::localize("Use Proxy", self::class, $text_options);
			case 'SWITCH_PROXY':
				/** @description "SWITCH_PROXY" label. */
				return UText::localize("Switch Proxy", self::class, $text_options);
			case 'TEMPORARY_REDIRECT':
				/** @description "TEMPORARY_REDIRECT" label. */
				return UText::localize("Temporary Redirect", self::class, $text_options);
			case 'PERMANENT_REDIRECT':
				/** @description "PERMANENT_REDIRECT" label. */
				return UText::localize("Permanent Redirect", self::class, $text_options);
				
			//client error (4xx)
			case 'BAD_REQUEST':
				/** @description "BAD_REQUEST" label. */
				return UText::localize("Bad Request", self::class, $text_options);
			case 'UNAUTHORIZED':
				/** @description "UNAUTHORIZED" label. */
				return UText::localize("Unauthorized", self::class, $text_options);
			case 'PAYMENT_REQUIRED':
				/** @description "PAYMENT_REQUIRED" label. */
				return UText::localize("Payment Required", self::class, $text_options);
			case 'FORBIDDEN':
				/** @description "FORBIDDEN" label. */
				return UText::localize("Forbidden", self::class, $text_options);
			case 'NOT_FOUND':
				/** @description "NOT_FOUND" label. */
				return UText::localize("Not Found", self::class, $text_options);
			case 'METHOD_NOT_ALLOWED':
				/** @description "METHOD_NOT_ALLOWED" label. */
				return UText::localize("Method Not Allowed", self::class, $text_options);
			case 'NOT_ACCEPTABLE':
				/** @description "NOT_ACCEPTABLE" label. */
				return UText::localize("Not Acceptable", self::class, $text_options);
			case 'PROXY_AUTHENTICATION_REQUIRED':
				/** @description "PROXY_AUTHENTICATION_REQUIRED" label. */
				return UText::localize("Proxy Authentication Required", self::class, $text_options);
			case 'REQUEST_TIMEOUT':
				/** @description "REQUEST_TIMEOUT" label. */
				return UText::localize("Request Timeout", self::class, $text_options);
			case 'CONFLICT':
				/** @description "CONFLICT" label. */
				return UText::localize("Conflict", self::class, $text_options);
			case 'GONE':
				/** @description "GONE" label. */
				return UText::localize("Gone", self::class, $text_options);
			case 'LENGTH_REQUIRED':
				/** @description "LENGTH_REQUIRED" label. */
				return UText::localize("Length Required", self::class, $text_options);
			case 'PRECONDITION_FAILED':
				/** @description "PRECONDITION_FAILED" label. */
				return UText::localize("Precondition Failed", self::class, $text_options);
			case 'PAYLOAD_TOO_LARGE':
				/** @description "PAYLOAD_TOO_LARGE" label. */
				return UText::localize("Payload Too Large", self::class, $text_options);
			case 'URI_TOO_LONG':
				/** @description "URI_TOO_LONG" label. */
				return UText::localize("URI Too Long", self::class, $text_options);
			case 'UNSUPPORTED_MEDIA_TYPE':
				/** @description "UNSUPPORTED_MEDIA_TYPE" label. */
				return UText::localize("Unsupported Media Type", self::class, $text_options);
			case 'RANGE_NOT_SATISFIABLE':
				/** @description "RANGE_NOT_SATISFIABLE" label. */
				return UText::localize("Range Not Satisfiable", self::class, $text_options);
			case 'EXPECTATION_FAILED':
				/** @description "EXPECTATION_FAILED" label. */
				return UText::localize("Expectation Failed", self::class, $text_options);
			case 'IM_A_TEAPOT':
				/** @description "IM_A_TEAPOT" label. */
				return UText::localize("I'm a Teapot", self::class, $text_options);
			case 'MISDIRECTED_REQUEST':
				/** @description "MISDIRECTED_REQUEST" label. */
				return UText::localize("Misdirected Request", self::class, $text_options);
			case 'UNPROCESSABLE_ENTITY':
				/** @description "UNPROCESSABLE_ENTITY" label. */
				return UText::localize("Unprocessable Entity", self::class, $text_options);
			case 'LOCKED':
				/** @description "LOCKED" label. */
				return UText::localize("Locked", self::class, $text_options);
			case 'FAILED_DEPENDENCY':
				/** @description "FAILED_DEPENDENCY" label. */
				return UText::localize("Failed Dependency", self::class, $text_options);
			case 'UPGRADE_REQUIRED':
				/** @description "UPGRADE_REQUIRED" label. */
				return UText::localize("Upgrade Required", self::class, $text_options);
			case 'PRECONDITION_REQUIRED':
				/** @description "PRECONDITION_REQUIRED" label. */
				return UText::localize("Precondition Required", self::class, $text_options);
			case 'TOO_MANY_REQUESTS':
				/** @description "TOO_MANY_REQUESTS" label. */
				return UText::localize("Too Many Requests", self::class, $text_options);
			case 'REQUEST_HEADER_FIELDS_TOO_LARGE':
				/** @description "REQUEST_HEADER_FIELDS_TOO_LARGE" label. */
				return UText::localize("Request Header Fields Too Large", self::class, $text_options);
			case 'UNAVAILABLE_FOR_LEGAL_REASONS':
				/** @description "UNAVAILABLE_FOR_LEGAL_REASONS" label. */
				return UText::localize("Unavailable For Legal Reasons", self::class, $text_options);
			
			//server error (5xx)
			case 'INTERNAL_SERVER_ERROR':
				/** @description "INTERNAL_SERVER_ERROR" label. */
				return UText::localize("Internal Server Error", self::class, $text_options);
			case 'NOT_IMPLEMENTED':
				/** @description "NOT_IMPLEMENTED" label. */
				return UText::localize("Not Implemented", self::class, $text_options);
			case 'BAD_GATEWAY':
				/** @description "BAD_GATEWAY" label. */
				return UText::localize("Bad Gateway", self::class, $text_options);
			case 'SERVICE_UNAVAILABLE':
				/** @description "SERVICE_UNAVAILABLE" label. */
				return UText::localize("Service Unavailable", self::class, $text_options);
			case 'GATEWAY_TIMEOUT':
				/** @description "GATEWAY_TIMEOUT" label. */
				return UText::localize("Gateway Timeout", self::class, $text_options);
			case 'HTTP_VERSION_NOT_SUPPORTED':
				/** @description "HTTP_VERSION_NOT_SUPPORTED" label. */
				return UText::localize("HTTP Version Not Supported", self::class, $text_options);
			case 'VARIANT_ALSO_NEGOTIATES':
				/** @description "VARIANT_ALSO_NEGOTIATES" label. */
				return UText::localize("Variant Also Negotiates", self::class, $text_options);
			case 'INSUFFICIENT_STORAGE':
				/** @description "INSUFFICIENT_STORAGE" label. */
				return UText::localize("Insufficient Storage", self::class, $text_options);
			case 'LOOP_DETECTED':
				/** @description "LOOP_DETECTED" label. */
				return UText::localize("Loop Detected", self::class, $text_options);
			case 'NOT_EXTENDED':
				/** @description "NOT_EXTENDED" label. */
				return UText::localize("Not Extended", self::class, $text_options);
			case 'NETWORK_AUTHENTICATION_REQUIRED':
				/** @description "NETWORK_AUTHENTICATION_REQUIRED" label. */
				return UText::localize("Network Authentication Required", self::class, $text_options);
		}
		return null;
	}
	
	/** {@inheritdoc} */
	protected static function returnDescription(string $name, TextOptions $text_options): ?string
	{
		//initialize
		$value = static::getNameValue($name);
		$label = static::returnLabel($name, $text_options);
		if (!isset($label)) {
			return null;
		}
		
		//description
		$label = UText::stringify($label, $text_options, ['quote_strings' => true]);
		if ($value <= 199) {
			/**
			 * @description Informational status code description.
			 * @placeholder label The HTTP status code label.
			 * @example Informational "Continue" HTTP status code.
			 */
			return UText::localize(
				"Informational {{label}} HTTP status code.",
				self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		} elseif ($value <= 299) {
			/**
			 * @description Success status code description.
			 * @placeholder label The HTTP status code label.
			 * @example Success "OK" HTTP status code.
			 */
			return UText::localize(
				"Success {{label}} HTTP status code.",
				self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		} elseif ($value <= 399) {
			/**
			 * @description Redirection status code description.
			 * @placeholder label The HTTP status code label.
			 * @example Redirection "Found" HTTP status code.
			 */
			return UText::localize(
				"Redirection {{label}} HTTP status code.",
				self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		} elseif ($value <= 499) {
			/**
			 * @description Client error status code description.
			 * @placeholder label The HTTP status code label.
			 * @example Client error "Bad Request" HTTP status code.
			 */
			return UText::localize(
				"Client error {{label}} HTTP status code.",
				self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		} elseif ($value <= 599) {
			/**
			 * @description Server error status code description.
			 * @placeholder label The HTTP status code label.
			 * @example Server error "Bad Gateway" HTTP status code.
			 */
			return UText::localize(
				"Server error {{label}} HTTP status code.",
				self::class, $text_options, ['parameters' => ['label' => $label]]
			);
		}
		return null;
	}
	
	
	
	//Public static methods
	/**
	 * Check if a given element represents an informational HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents an informational HTTP status.</p>
	 */
	public static function isInformational($element): bool
	{
		$value = static::getValue($element);
		return $value >= 100 && $value <= 199;
	}
	
	/**
	 * Check if a given element represents a success HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a success HTTP status.</p>
	 */
	public static function isSuccess($element): bool
	{
		$value = static::getValue($element);
		return $value >= 200 && $value <= 299;
	}
	
	/**
	 * Check if a given element represents a redirection HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a redirection HTTP status.</p>
	 */
	public static function isRedirection($element): bool
	{
		$value = static::getValue($element);
		return $value >= 300 && $value <= 399;
	}
	
	/**
	 * Check if a given element represents a client error HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a client error HTTP status.</p>
	 */
	public static function isClientError($element): bool
	{
		$value = static::getValue($element);
		return $value >= 400 && $value <= 499;
	}
	
	/**
	 * Check if a given element represents a server error HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents a server error HTTP status.</p>
	 */
	public static function isServerError($element): bool
	{
		$value = static::getValue($element);
		return $value >= 500 && $value <= 599;
	}
	
	/**
	 * Check if a given element represents an error HTTP status.
	 * 
	 * @param int|string $element
	 * <p>The element to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given element represents an error HTTP status.</p>
	 */
	public static function isError($element): bool
	{
		$value = static::getValue($element);
		return $value >= 400 && $value <= 599;
	}
}
