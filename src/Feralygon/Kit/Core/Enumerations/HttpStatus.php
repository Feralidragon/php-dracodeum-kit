<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumerations;

use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Utilities\Text as UText;
use Feralygon\Kit\Core\Options\Text as TextOptions;

/**
 * Core HTTP status enumeration class.
 * 
 * This enumeration represents HTTP response status codes.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
 */
class HttpStatus extends Enumeration
{
	//Public constants
	/** Informational "Continue" HTTP status code. */
	public const CONTINUE = 100;
	
	/** Informational "Switching Protocols" HTTP status code. */
	public const SWITCHING_PROTOCOLS = 101;
	
	/** Informational "Processing" HTTP status code. */
	public const PROCESSING = 102;
	
	/** Success "OK" HTTP status code. */
	public const OK = 200;
	
	/** Success "Created" HTTP status code. */
	public const CREATED = 201;
	
	/** Success "Accepted" HTTP status code. */
	public const ACCEPTED = 202;
	
	/** Success "Non-Authoritative Information" HTTP status code. */
	public const NON_AUTHORITATIVE_INFORMATION = 203;
	
	/** Success "No Content" HTTP status code. */
	public const NO_CONTENT = 204;
	
	/** Success "Reset Content" HTTP status code. */
	public const RESET_CONTENT = 205;
	
	/** Success "Partial Content" HTTP status code. */
	public const PARTIAL_CONTENT = 206;
	
	/** Success "Multi-Status" HTTP status code. */
	public const MULTI_STATUS = 207;
	
	/** Success "Already Reported" HTTP status code. */
	public const ALREADY_REPORTED = 208;
	
	/** Success "IM Used" HTTP status code. */
	public const IM_USED = 226;
	
	/** Redirection "Multiple Choices" HTTP status code. */
	public const MULTIPLE_CHOICES = 300;
	
	/** Redirection "Moved Permanently" HTTP status code. */
	public const MOVED_PERMANENTLY = 301;
	
	/** Redirection "Found" HTTP status code. */
	public const FOUND = 302;
	
	/** Redirection "See Other" HTTP status code. */
	public const SEE_OTHER = 303;
	
	/** Redirection "Not Modified" HTTP status code. */
	public const NOT_MODIFIED = 304;
	
	/** Redirection "Use Proxy" HTTP status code. */
	public const USE_PROXY = 305;
	
	/** Redirection "Switch Proxy" HTTP status code. */
	public const SWITCH_PROXY = 306;
	
	/** Redirection "Temporary Redirect" HTTP status code. */
	public const TEMPORARY_REDIRECT = 307;
	
	/** Redirection "Permanent Redirect" HTTP status code. */
	public const PERMANENT_REDIRECT = 308;
	
	/** Client error "Bad Request" HTTP status code. */
	public const BAD_REQUEST = 400;
	
	/** Client error "Unauthorized" HTTP status code. */
	public const UNAUTHORIZED = 401;
	
	/** Client error "Payment Required" HTTP status code. */
	public const PAYMENT_REQUIRED = 402;
	
	/** Client error "Forbidden" HTTP status code. */
	public const FORBIDDEN = 403;
	
	/** Client error "Not Found" HTTP status code. */
	public const NOT_FOUND = 404;
	
	/** Client error "Method Not Allowed" HTTP status code. */
	public const METHOD_NOT_ALLOWED = 405;
	
	/** Client error "Not Acceptable" HTTP status code. */
	public const NOT_ACCEPTABLE = 406;
	
	/** Client error "Proxy Authentication Required" HTTP status code. */
	public const PROXY_AUTHENTICATION_REQUIRED = 407;
	
	/** Client error "Request Timeout" HTTP status code. */
	public const REQUEST_TIMEOUT = 408;
	
	/** Client error "Conflict" HTTP status code. */
	public const CONFLICT = 409;
	
	/** Client error "Gone" HTTP status code. */
	public const GONE = 410;
	
	/** Client error "Length Required" HTTP status code. */
	public const LENGTH_REQUIRED = 411;
	
	/** Client error "Precondition Failed" HTTP status code. */
	public const PRECONDITION_FAILED = 412;
	
	/** Client error "Payload Too Large" HTTP status code. */
	public const PAYLOAD_TOO_LARGE = 413;
	
	/** Client error "URI Too Long" HTTP status code. */
	public const URI_TOO_LONG = 414;
	
	/** Client error "Unsupported Media Type" HTTP status code. */
	public const UNSUPPORTED_MEDIA_TYPE = 415;
	
	/** Client error "Range Not Satisfiable" HTTP status code. */
	public const RANGE_NOT_SATISFIABLE = 416;
	
	/** Client error "Expectation Failed" HTTP status code. */
	public const EXPECTATION_FAILED = 417;
	
	/** Client error "I'm a teapot" HTTP status code. */
	public const IM_A_TEAPOT = 418;
	
	/** Client error "Misdirected Request" HTTP status code. */
	public const MISDIRECTED_REQUEST = 421;
	
	/** Client error "Unprocessable Entity" HTTP status code. */
	public const UNPROCESSABLE_ENTITY = 422;
	
	/** Client error "Locked" HTTP status code. */
	public const LOCKED = 423;
	
	/** Client error "Failed Dependency" HTTP status code. */
	public const FAILED_DEPENDENCY = 424;
	
	/** Client error "Upgrade Required" HTTP status code. */
	public const UPGRADE_REQUIRED = 426;
	
	/** Client error "Precondition Required" HTTP status code. */
	public const PRECONDITION_REQUIRED = 428;
	
	/** Client error "Too Many Requests" HTTP status code. */
	public const TOO_MANY_REQUESTS = 429;
	
	/** Client error "Request Header Fields Too Large" HTTP status code. */
	public const REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
	
	/** Client error "Unavailable For Legal Reasons" HTTP status code. */
	public const UNAVAILABLE_FOR_LEGAL_REASONS = 451;
	
	/** Server error "Internal Server Error" HTTP status code. */
	public const INTERNAL_SERVER_ERROR = 500;
	
	/** Server error "Not Implemented" HTTP status code. */
	public const NOT_IMPLEMENTED = 501;
	
	/** Server error "Bad Gateway" HTTP status code. */
	public const BAD_GATEWAY = 502;
	
	/** Server error "Service Unavailable" HTTP status code. */
	public const SERVICE_UNAVAILABLE = 503;
	
	/** Server error "Gateway Timeout" HTTP status code. */
	public const GATEWAY_TIMEOUT = 504;
	
	/** Server error "HTTP Version Not Supported" HTTP status code. */
	public const HTTP_VERSION_NOT_SUPPORTED = 505;
	
	/** Server error "Variant Also Negotiates" HTTP status code. */
	public const VARIANT_ALSO_NEGOTIATES = 506;
	
	/** Server error "Insufficient Storage" HTTP status code. */
	public const INSUFFICIENT_STORAGE = 507;
	
	/** Server error "Loop Detected" HTTP status code. */
	public const LOOP_DETECTED = 508;
	
	/** Server error "Not Extended" HTTP status code. */
	public const NOT_EXTENDED = 510;
	
	/** Server error "Network Authentication Required" HTTP status code. */
	public const NETWORK_AUTHENTICATION_REQUIRED = 511;
	
	
	
	//Implemented protected static methods (core enumeration information trait)
	/** {@inheritdoc} */
	protected static function retrieveLabel(string $name, TextOptions $text_options) : ?string
	{
		switch ($name) {
			//informational (1xx)
			case 'CONTINUE':
				/**
				 * @description Core HTTP status enumeration informational "CONTINUE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Continue", 'core.enumerations.http_status', $text_options);
			case 'SWITCHING_PROTOCOLS':
				/**
				 * @description Core HTTP status enumeration informational "SWITCHING_PROTOCOLS" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Switching Protocols", 'core.enumerations.http_status', $text_options);
			case 'PROCESSING':
				/**
				 * @description Core HTTP status enumeration informational "PROCESSING" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Processing", 'core.enumerations.http_status', $text_options);
				
			//success (2xx)
			case 'OK':
				/**
				 * @description Core HTTP status enumeration success "OK" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("OK", 'core.enumerations.http_status', $text_options);
			case 'CREATED':
				/**
				 * @description Core HTTP status enumeration success "CREATED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Created", 'core.enumerations.http_status', $text_options);
			case 'ACCEPTED':
				/**
				 * @description Core HTTP status enumeration success "ACCEPTED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Accepted", 'core.enumerations.http_status', $text_options);
			case 'NON_AUTHORITATIVE_INFORMATION':
				/**
				 * @description Core HTTP status enumeration success "NON_AUTHORITATIVE_INFORMATION" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Non-Authoritative Information", 'core.enumerations.http_status', $text_options);
			case 'NO_CONTENT':
				/**
				 * @description Core HTTP status enumeration success "NO_CONTENT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("No Content", 'core.enumerations.http_status', $text_options);
			case 'RESET_CONTENT':
				/**
				 * @description Core HTTP status enumeration success "RESET_CONTENT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Reset Content", 'core.enumerations.http_status', $text_options);
			case 'PARTIAL_CONTENT':
				/**
				 * @description Core HTTP status enumeration success "PARTIAL_CONTENT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Partial Content", 'core.enumerations.http_status', $text_options);
			case 'MULTI_STATUS':
				/**
				 * @description Core HTTP status enumeration success "MULTI_STATUS" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Multi-Status", 'core.enumerations.http_status', $text_options);
			case 'ALREADY_REPORTED':
				/**
				 * @description Core HTTP status enumeration success "ALREADY_REPORTED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Already Reported", 'core.enumerations.http_status', $text_options);
			case 'IM_USED':
				/**
				 * @description Core HTTP status enumeration success "IM_USED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("IM Used", 'core.enumerations.http_status', $text_options);
				
			//redirection (3xx)
			case 'MULTIPLE_CHOICES':
				/**
				 * @description Core HTTP status enumeration redirection "MULTIPLE_CHOICES" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Multiple Choices", 'core.enumerations.http_status', $text_options);
			case 'MOVED_PERMANENTLY':
				/**
				 * @description Core HTTP status enumeration redirection "MOVED_PERMANENTLY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Moved Permanently", 'core.enumerations.http_status', $text_options);
			case 'FOUND':
				/**
				 * @description Core HTTP status enumeration redirection "FOUND" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Found", 'core.enumerations.http_status', $text_options);
			case 'SEE_OTHER':
				/**
				 * @description Core HTTP status enumeration redirection "SEE_OTHER" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("See Other", 'core.enumerations.http_status', $text_options);
			case 'NOT_MODIFIED':
				/**
				 * @description Core HTTP status enumeration redirection "NOT_MODIFIED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Not Modified", 'core.enumerations.http_status', $text_options);
			case 'USE_PROXY':
				/**
				 * @description Core HTTP status enumeration redirection "USE_PROXY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Use Proxy", 'core.enumerations.http_status', $text_options);
			case 'SWITCH_PROXY':
				/**
				 * @description Core HTTP status enumeration redirection "SWITCH_PROXY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Switch Proxy", 'core.enumerations.http_status', $text_options);
			case 'TEMPORARY_REDIRECT':
				/**
				 * @description Core HTTP status enumeration redirection "TEMPORARY_REDIRECT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Temporary Redirect", 'core.enumerations.http_status', $text_options);
			case 'PERMANENT_REDIRECT':
				/**
				 * @description Core HTTP status enumeration redirection "PERMANENT_REDIRECT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Permanent Redirect", 'core.enumerations.http_status', $text_options);
				
			//client error (4xx)
			case 'BAD_REQUEST':
				/**
				 * @description Core HTTP status enumeration client error "BAD_REQUEST" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Bad Request", 'core.enumerations.http_status', $text_options);
			case 'UNAUTHORIZED':
				/**
				 * @description Core HTTP status enumeration client error "UNAUTHORIZED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Unauthorized", 'core.enumerations.http_status', $text_options);
			case 'PAYMENT_REQUIRED':
				/**
				 * @description Core HTTP status enumeration client error "PAYMENT_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Payment Required", 'core.enumerations.http_status', $text_options);
			case 'FORBIDDEN':
				/**
				 * @description Core HTTP status enumeration client error "FORBIDDEN" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Forbidden", 'core.enumerations.http_status', $text_options);
			case 'NOT_FOUND':
				/**
				 * @description Core HTTP status enumeration client error "NOT_FOUND" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Not Found", 'core.enumerations.http_status', $text_options);
			case 'METHOD_NOT_ALLOWED':
				/**
				 * @description Core HTTP status enumeration client error "METHOD_NOT_ALLOWED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Method Not Allowed", 'core.enumerations.http_status', $text_options);
			case 'NOT_ACCEPTABLE':
				/**
				 * @description Core HTTP status enumeration client error "NOT_ACCEPTABLE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Not Acceptable", 'core.enumerations.http_status', $text_options);
			case 'PROXY_AUTHENTICATION_REQUIRED':
				/**
				 * @description Core HTTP status enumeration client error "PROXY_AUTHENTICATION_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Proxy Authentication Required", 'core.enumerations.http_status', $text_options);
			case 'REQUEST_TIMEOUT':
				/**
				 * @description Core HTTP status enumeration client error "REQUEST_TIMEOUT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Request Timeout", 'core.enumerations.http_status', $text_options);
			case 'CONFLICT':
				/**
				 * @description Core HTTP status enumeration client error "CONFLICT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Conflict", 'core.enumerations.http_status', $text_options);
			case 'GONE':
				/**
				 * @description Core HTTP status enumeration client error "GONE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Gone", 'core.enumerations.http_status', $text_options);
			case 'LENGTH_REQUIRED':
				/**
				 * @description Core HTTP status enumeration client error "LENGTH_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Length Required", 'core.enumerations.http_status', $text_options);
			case 'PRECONDITION_FAILED':
				/**
				 * @description Core HTTP status enumeration client error "PRECONDITION_FAILED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Precondition Failed", 'core.enumerations.http_status', $text_options);
			case 'PAYLOAD_TOO_LARGE':
				/**
				 * @description Core HTTP status enumeration client error "PAYLOAD_TOO_LARGE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Payload Too Large", 'core.enumerations.http_status', $text_options);
			case 'URI_TOO_LONG':
				/**
				 * @description Core HTTP status enumeration client error "URI_TOO_LONG" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("URI Too Long", 'core.enumerations.http_status', $text_options);
			case 'UNSUPPORTED_MEDIA_TYPE':
				/**
				 * @description Core HTTP status enumeration client error "UNSUPPORTED_MEDIA_TYPE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Unsupported Media Type", 'core.enumerations.http_status', $text_options);
			case 'RANGE_NOT_SATISFIABLE':
				/**
				 * @description Core HTTP status enumeration client error "RANGE_NOT_SATISFIABLE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Range Not Satisfiable", 'core.enumerations.http_status', $text_options);
			case 'EXPECTATION_FAILED':
				/**
				 * @description Core HTTP status enumeration client error "EXPECTATION_FAILED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Expectation Failed", 'core.enumerations.http_status', $text_options);
			case 'IM_A_TEAPOT':
				/**
				 * @description Core HTTP status enumeration client error "IM_A_TEAPOT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("I'm a Teapot", 'core.enumerations.http_status', $text_options);
			case 'MISDIRECTED_REQUEST':
				/**
				 * @description Core HTTP status enumeration client error "MISDIRECTED_REQUEST" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Misdirected Request", 'core.enumerations.http_status', $text_options);
			case 'UNPROCESSABLE_ENTITY':
				/**
				 * @description Core HTTP status enumeration client error "UNPROCESSABLE_ENTITY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Unprocessable Entity", 'core.enumerations.http_status', $text_options);
			case 'LOCKED':
				/**
				 * @description Core HTTP status enumeration client error "LOCKED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Locked", 'core.enumerations.http_status', $text_options);
			case 'FAILED_DEPENDENCY':
				/**
				 * @description Core HTTP status enumeration client error "FAILED_DEPENDENCY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Failed Dependency", 'core.enumerations.http_status', $text_options);
			case 'UPGRADE_REQUIRED':
				/**
				 * @description Core HTTP status enumeration client error "UPGRADE_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Upgrade Required", 'core.enumerations.http_status', $text_options);
			case 'PRECONDITION_REQUIRED':
				/**
				 * @description Core HTTP status enumeration client error "PRECONDITION_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Precondition Required", 'core.enumerations.http_status', $text_options);
			case 'TOO_MANY_REQUESTS':
				/**
				 * @description Core HTTP status enumeration client error "TOO_MANY_REQUESTS" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Too Many Requests", 'core.enumerations.http_status', $text_options);
			case 'REQUEST_HEADER_FIELDS_TOO_LARGE':
				/**
				 * @description Core HTTP status enumeration client error "REQUEST_HEADER_FIELDS_TOO_LARGE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Request Header Fields Too Large", 'core.enumerations.http_status', $text_options);
			case 'UNAVAILABLE_FOR_LEGAL_REASONS':
				/**
				 * @description Core HTTP status enumeration client error "UNAVAILABLE_FOR_LEGAL_REASONS" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Unavailable For Legal Reasons", 'core.enumerations.http_status', $text_options);
			
			//server error (5xx)
			case 'INTERNAL_SERVER_ERROR':
				/**
				 * @description Core HTTP status enumeration server error "INTERNAL_SERVER_ERROR" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Internal Server Error", 'core.enumerations.http_status', $text_options);
			case 'NOT_IMPLEMENTED':
				/**
				 * @description Core HTTP status enumeration server error "NOT_IMPLEMENTED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Not Implemented", 'core.enumerations.http_status', $text_options);
			case 'BAD_GATEWAY':
				/**
				 * @description Core HTTP status enumeration server error "BAD_GATEWAY" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Bad Gateway", 'core.enumerations.http_status', $text_options);
			case 'SERVICE_UNAVAILABLE':
				/**
				 * @description Core HTTP status enumeration server error "SERVICE_UNAVAILABLE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Service Unavailable", 'core.enumerations.http_status', $text_options);
			case 'GATEWAY_TIMEOUT':
				/**
				 * @description Core HTTP status enumeration server error "GATEWAY_TIMEOUT" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Gateway Timeout", 'core.enumerations.http_status', $text_options);
			case 'HTTP_VERSION_NOT_SUPPORTED':
				/**
				 * @description Core HTTP status enumeration server error "HTTP_VERSION_NOT_SUPPORTED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("HTTP Version Not Supported", 'core.enumerations.http_status', $text_options);
			case 'VARIANT_ALSO_NEGOTIATES':
				/**
				 * @description Core HTTP status enumeration server error "VARIANT_ALSO_NEGOTIATES" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Variant Also Negotiates", 'core.enumerations.http_status', $text_options);
			case 'INSUFFICIENT_STORAGE':
				/**
				 * @description Core HTTP status enumeration server error "INSUFFICIENT_STORAGE" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Insufficient Storage", 'core.enumerations.http_status', $text_options);
			case 'LOOP_DETECTED':
				/**
				 * @description Core HTTP status enumeration server error "LOOP_DETECTED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Loop Detected", 'core.enumerations.http_status', $text_options);
			case 'NOT_EXTENDED':
				/**
				 * @description Core HTTP status enumeration server error "NOT_EXTENDED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Not Extended", 'core.enumerations.http_status', $text_options);
			case 'NETWORK_AUTHENTICATION_REQUIRED':
				/**
				 * @description Core HTTP status enumeration server error "NETWORK_AUTHENTICATION_REQUIRED" label.
				 * @tags core enumeration http status label
				 */
				return UText::localize("Network Authentication Required", 'core.enumerations.http_status', $text_options);
		}
		return null;
	}
	
	/** {@inheritdoc} */
	protected static function retrieveDescription(string $name, TextOptions $text_options) : ?string
	{
		//initialize
		$value = static::getNameValue($name);
		$label = static::retrieveLabel($name, $text_options);
		if (!isset($label)) {
			return null;
		}
		
		//description
		$label = UText::stringify($label, $text_options);
		if ($value <= 199) {
			/**
			 * @description Core HTTP status enumeration informational description.
			 * @placeholder label The HTTP status label.
			 * @tags core enumeration http status description
			 * @example Informational "Continue" HTTP status code.
			 */
			return UText::localize("Informational {{label}} HTTP status code.", 'core.enumerations.http_status', $text_options, ['parameters' => ['label' => $label]]);
		} elseif ($value <= 299) {
			/**
			 * @description Core HTTP status enumeration success description.
			 * @placeholder label The HTTP status label.
			 * @tags core enumeration http status description
			 * @example Success "OK" HTTP status code.
			 */
			return UText::localize("Success {{label}} HTTP status code.", 'core.enumerations.http_status', $text_options, ['parameters' => ['label' => $label]]);
		} elseif ($value <= 399) {
			/**
			 * @description Core HTTP status enumeration redirection description.
			 * @placeholder label The HTTP status label.
			 * @tags core enumeration http status description
			 * @example Redirection "Found" HTTP status code.
			 */
			return UText::localize("Redirection {{label}} HTTP status code.", 'core.enumerations.http_status', $text_options, ['parameters' => ['label' => $label]]);
		} elseif ($value <= 499) {
			/**
			 * @description Core HTTP status enumeration client error description.
			 * @placeholder label The HTTP status label.
			 * @tags core enumeration http status description
			 * @example Client error "Bad Request" HTTP status code.
			 */
			return UText::localize("Client error {{label}} HTTP status code.", 'core.enumerations.http_status', $text_options, ['parameters' => ['label' => $label]]);
		} elseif ($value <= 599) {
			/**
			 * @description Core HTTP status enumeration server error description.
			 * @placeholder label The HTTP status label.
			 * @tags core enumeration http status description
			 * @example Server error "Bad Gateway" HTTP status code.
			 */
			return UText::localize("Server error {{label}} HTTP status code.", 'core.enumerations.http_status', $text_options, ['parameters' => ['label' => $label]]);
		}
		return null;
	}
}
