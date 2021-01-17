<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 7235 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc7235#appendix-C
 */
class Rfc7235 extends Enumeration
{
	//Public constants
	/** <samp>auth-param</samp> */
	public const AUTH_PARAM = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>auth-scheme</samp> */
	public const AUTH_SCHEME = self::TOKEN;
	
	/** <samp>challenge</samp> */
	public const CHALLENGE = '(?:' . self::AUTH_SCHEME . 
		'(?:' . Rfc5234::SP . '+(?:' . self::TOKEN68 . '|(?:(?:\,|' . self::AUTH_PARAM . ')' . 
			'(?:' . self::OWS . '\,(?:' . self::OWS . self::AUTH_PARAM . ')?)*)?))?' . 
		')';
	
	/** <samp>credentials</samp> */
	public const CREDENTIALS = '(?:' . self::AUTH_SCHEME . 
		'(?:' . Rfc5234::SP . '+(?:' . self::TOKEN68 . '|(?:(?:\,|' . self::AUTH_PARAM . ')' . 
			'(?:' . self::OWS . '\,(?:' . self::OWS . self::AUTH_PARAM . ')?)*)?))?' . 
		')';
	
	/** <samp>OWS</samp> */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>Proxy-Authenticate</samp> */
	public const PROXY_AUTHENTICATE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CHALLENGE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CHALLENGE . ')?)*' . 
		')';
	
	/** <samp>Proxy-Authorization</samp> */
	public const PROXY_AUTHORIZATION = self::CREDENTIALS;
	
	/** <samp>quoted-string</samp> */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>token</samp> */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>token68</samp> */
	public const TOKEN68 = '(?:(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\-\.\_\~\+\/])+\=*)';
	
	/** <samp>WWW-Authenticate</samp> */
	public const WWW_AUTHENTICATE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CHALLENGE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CHALLENGE . ')?)*' . 
		')';
}
