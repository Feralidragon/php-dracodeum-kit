<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Http;

use Feralygon\Kit\Enumeration;
use Feralygon\Kit\Enumerations\Abnf\Pattern as EAbnfPattern;
use Feralygon\Kit\Enumerations\Uri\Pattern as EUriPattern;

/**
 * This enumeration represents HTTP specification regular expression patterns.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc3986#section-3.1
 * @see https://tools.ietf.org/html/rfc7230#section-3.2.3
 * @see https://tools.ietf.org/html/rfc7230#section-3.2.6
 * @see https://tools.ietf.org/html/rfc7230#appendix-B
 * @see https://tools.ietf.org/html/rfc7235#appendix-C
 */
class Pattern extends Enumeration
{
	//Public constants
	/** <samp>scheme</samp> regular expression pattern. */
	public const SCHEME = EUriPattern::SCHEME;
	
	/** <samp>OWS</samp> regular expression pattern. */
	public const OWS = '(?:[\t\ ]*)';
	
	/** <samp>RWS</samp> regular expression pattern. */
	public const RWS = '(?:[\t\ ]+)';
	
	/** <samp>VCHAR</samp> regular expression pattern. */
	public const VCHAR = EAbnfPattern::VCHAR;
	
	/** <samp>VCHAR-WS</samp> regular expression pattern. */
	public const VCHAR_WS = '(?:[\t\ ]|' . self::VCHAR . ')';
	
	/** <samp>tchar</samp> regular expression pattern. */
	public const TCHAR = '[\!\#\$\%\&\\\'\*\+\-\.\^\`\|\~\w]';
	
	/** <samp>token</samp> regular expression pattern. */
	public const TOKEN = '(?:' . self::TCHAR . '+)';
	
	/** <samp>token68</samp> regular expression pattern. */
	public const TOKEN68 = '(?:[\w\-\.\~\+\/]+\=*)';
	
	/** <samp>qdtext</samp> regular expression pattern. */
	public const QDTEXT = '[\t\ \!\x23-\x5b\x5d-\x7e]';
	
	/** <samp>quoted-pair</samp> regular expression pattern. */
	public const QUOTED_PAIR = '(?:\\\\[\t\ \x21-\x7e])';
	
	/** <samp>quoted-string</samp> regular expression pattern. */
	public const QUOTED_STRING = '(?:\"(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*\")';
	
	/** <samp>ctext</samp> regular expression pattern. */
	public const CTEXT = '[\t\ \x21-\x27\x2a-\x5b\x5d-\x7e]';
	
	/** <samp>comment</samp> regular expression pattern. */
	public const COMMENT = '(?:\((?:' . self::CTEXT . '|' . self::QUOTED_PAIR . ')*\))';
	
	/** <samp>qvalue</samp> regular expression pattern. */
	public const QVALUE = '(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)';
	
	/** <samp>weight</samp> regular expression pattern. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . '[Qq]\=' . self::QVALUE . ')';
	
	/** <samp>parameter</samp> regular expression pattern. */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
}
