<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 7235 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc7235#appendix-C
 */
class Rfc7235 extends Enumeration
{
	//Public constants
	/** <samp>auth-param</samp> ABNF regular expression. */
	public const AUTH_PARAM = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>auth-scheme</samp> ABNF regular expression. */
	public const AUTH_SCHEME = self::TOKEN;
	
	/** <samp>challenge</samp> ABNF regular expression. */
	public const CHALLENGE = '(?:' . self::AUTH_SCHEME . 
		'(?:' . Rfc5234::SP . '+(?:' . self::TOKEN68 . '|(?:(?:\,|' . self::AUTH_PARAM . ')' . 
			'(?:' . self::OWS . '\,(?:' . self::OWS . self::AUTH_PARAM . ')?)*)?))?' . 
		')';
	
	/** <samp>credentials</samp> ABNF regular expression. */
	public const CREDENTIALS = '(?:' . self::AUTH_SCHEME . 
		'(?:' . Rfc5234::SP . '+(?:' . self::TOKEN68 . '|(?:(?:\,|' . self::AUTH_PARAM . ')' . 
			'(?:' . self::OWS . '\,(?:' . self::OWS . self::AUTH_PARAM . ')?)*)?))?' . 
		')';
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>Proxy-Authenticate</samp> ABNF regular expression. */
	public const PROXY_AUTHENTICATE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CHALLENGE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CHALLENGE . ')?)*' . 
		')';
	
	/** <samp>Proxy-Authorization</samp> ABNF regular expression. */
	public const PROXY_AUTHORIZATION = self::CREDENTIALS;
	
	/** <samp>quoted-string</samp> ABNF regular expression. */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>token</samp> ABNF regular expression. */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>token68</samp> ABNF regular expression. */
	public const TOKEN68 = '(?:(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\-\.\_\~\+\/])+\=*)';
	
	/** <samp>WWW-Authenticate</samp> ABNF regular expression. */
	public const WWW_AUTHENTICATE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CHALLENGE . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CHALLENGE . ')?)*' . 
		')';
}
