<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 7234 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc7234#appendix-C
 */
class Rfc7234 extends Enumeration
{
	//Public constants
	/** <samp>Age</samp> */
	public const AGE = self::DELTA_SECONDS;
	
	/** <samp>Cache-Control</samp> */
	public const CACHE_CONTROL = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CACHE_DIRECTIVE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CACHE_DIRECTIVE . ')?)*' . 
		')';
	
	/** <samp>cache-directive</samp> */
	public const CACHE_DIRECTIVE = '(?:' . self::TOKEN . '(?:\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))?)';
	
	/** <samp>delta-seconds</samp> */
	public const DELTA_SECONDS = '(?:' . Rfc5234::DIGIT . '+)';
	
	/** <samp>Expires</samp> */
	public const EXPIRES = self::HTTP_DATE;
	
	/** <samp>extension-pragma</samp> */
	public const EXTENSION_PRAGMA = '(?:' . self::TOKEN . '(?:\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))?)';
	
	/** <samp>field-name</samp> */
	public const FIELD_NAME = Rfc7230::FIELD_NAME;
	
	/** <samp>HTTP-date</samp> */
	public const HTTP_DATE = Rfc7231::HTTP_DATE;
	
	/** <samp>OWS</samp> */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>port</samp> */
	public const PORT = Rfc7230::PORT;
	
	/** <samp>Pragma</samp> */
	public const PRAGMA = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::PRAGMA_DIRECTIVE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::PRAGMA_DIRECTIVE . ')?)*' . 
		')';
	
	/** <samp>pragma-directive</samp> */
	public const PRAGMA_DIRECTIVE = '(?:no\-cache|' . self::EXTENSION_PRAGMA . ')';
	
	/** <samp>pseudonym</samp> */
	public const PSEUDONYM = Rfc7230::PSEUDONYM;
	
	/** <samp>quoted-string</samp> */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>token</samp> */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>uri-host</samp> */
	public const URI_HOST = Rfc7230::URI_HOST;
	
	/** <samp>Warning</samp> */
	public const WARNING = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::WARNING_VALUE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::WARNING_VALUE . ')?)*' . 
		')';
	
	/** <samp>warn-agent</samp> */
	public const WARN_AGENT = '(?:' . self::URI_HOST . '(?:\:' . self::PORT . ')?|' . self::PSEUDONYM . ')';
	
	/** <samp>warn-code</samp> */
	public const WARN_CODE = '(?:' . Rfc5234::DIGIT . '{3})';
	
	/** <samp>warn-date</samp> */
	public const WARN_DATE = '(?:' . Rfc5234::DQUOTE . self::HTTP_DATE . Rfc5234::DQUOTE . ')';
	
	/** <samp>warn-text</samp> */
	public const WARN_TEXT = self::QUOTED_STRING;
	
	/** <samp>warning-value</samp> */
	public const WARNING_VALUE = '(?:' . 
		self::WARN_CODE . Rfc5234::SP . self::WARN_AGENT . Rfc5234::SP . self::WARN_TEXT . 
		'(?:' . Rfc5234::SP . self::WARN_DATE . ')?' . 
		')';
}
