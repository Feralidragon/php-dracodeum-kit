<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Http;

use Feralygon\Kit\Enumeration;
use Feralygon\Kit\Enumerations\Abnf\Pattern as EAbnfPattern;

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
	/** SCHEME regular expression pattern. */
	public const SCHEME = '(?:[A-z][A-z0-9\+\-\.]*)';
	
	/** OWS regular expression pattern. */
	public const OWS = '(?:[\t\ ]*)';
	
	/** RWS regular expression pattern. */
	public const RWS = '(?:[\t\ ]+)';
	
	/** VCHAR regular expression pattern. */
	public const VCHAR = EAbnfPattern::VCHAR;
	
	/** VCHAR-WS regular expression pattern. */
	public const VCHAR_WS = '(?:[\t\ ]|' . self::VCHAR . ')';
	
	/** TCHAR regular expression pattern. */
	public const TCHAR = '[\!\#\$\%\&\\\'\*\+\-\.\^\`\|\~\w]';
	
	/** TOKEN regular expression pattern. */
	public const TOKEN = '(?:' . self::TCHAR . '+)';
	
	/** TOKEN68 regular expression pattern. */
	public const TOKEN68 = '(?:[\w\-\.\~\+\/]+\=*)';
	
	/** QDTEXT regular expression pattern. */
	public const QDTEXT = '[\t\ \!\x23-\x5b\x5d-\x7e]';
	
	/** QUOTED-PAIR regular expression pattern. */
	public const QUOTED_PAIR = '(?:\\\\[\t\ \x21-\x7e])';
	
	/** QUOTED-STRING regular expression pattern. */
	public const QUOTED_STRING = '(?:\"(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*\")';
	
	/** CTEXT regular expression pattern. */
	public const CTEXT = '[\t\ \x21-x27\x2a-\x5b\x5d-\x7e]';
	
	/** COMMENT regular expression pattern. */
	public const COMMENT = '(?:\((?:' . self::CTEXT . '|' . self::QUOTED_PAIR . ')*\))';
	
	/** QVALUE regular expression pattern. */
	public const QVALUE = '(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)';
	
	/** WEIGHT regular expression pattern. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . 'q\=' . self::QVALUE . ')';
	
	/** PARAMETER regular expression pattern. */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
}
