<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 5646 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc5646#section-2.1
 */
class Rfc5646 extends Enumeration
{
	//Public constants
	/** <samp>alphanum</samp> ABNF regular expression. */
	public const ALPHANUM = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . ')';
	
	/** <samp>extension</samp> ABNF regular expression. */
	public const EXTENSION = '(?:' . self::SINGLETON . '(?:\-' . self::ALPHANUM . '{2,8})+)';
	
	/** <samp>extlang</samp> ABNF regular expression. */
	public const EXTLANG = '(?:' . Rfc5234::ALPHA . '{3}(?:\-' . Rfc5234::ALPHA . '{3}){0,2})';
	
	/** <samp>grandfathered</samp> ABNF regular expression. */
	public const GRANDFATHERED = '(?:' . self::IRREGULAR . '|' . self::REGULAR . ')';
	
	/** <samp>irregular</samp> ABNF regular expression. */
	public const IRREGULAR = '(?:' . 
		'en\-GB\-oed|' . 
		'i\-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|tao|tay|tsu)|' . 
		'sgn\-(?:BE(?:FR|NL)|CH\-DE)' . 
		')';
	
	/** <samp>langtag</samp> ABNF regular expression. */
	public const LANGTAG = '(?:' . self::LANGUAGE . '(?:\-' . self::SCRIPT . ')?(?:\-' . self::REGION . ')?' . 
		'(?:\-' . self::VARIANT . ')*(?:\-' . self::EXTENSION . ')*(?:\-' . self::PRIVATEUSE . ')?)';
	
	/** <samp>language</samp> ABNF regular expression. */
	public const LANGUAGE = '(?:' . Rfc5234::ALPHA . '{2,3}(?:\-' . self::EXTLANG . ')?|' . Rfc5234::ALPHA . '{4,8})';
	
	/** <samp>language-tag</samp> ABNF regular expression. */
	public const LANGUAGE_TAG = '(?:' . self::LANGTAG . '|' . self::PRIVATEUSE . '|' . self::GRANDFATHERED . ')';
	
	/** <samp>privateuse</samp> ABNF regular expression. */
	public const PRIVATEUSE = '(?:x(?:\-' . self::ALPHANUM . '{1,8})+)';
	
	/** <samp>region</samp> ABNF regular expression. */
	public const REGION = '(?:' . Rfc5234::ALPHA . '{2}|' . Rfc5234::DIGIT . '{3})';
	
	/** <samp>regular</samp> ABNF regular expression. */
	public const REGULAR = '(?:art\-lojban|cel\-gaulish|no\-(?:bok|nyn)|zh\-(?:guoyu|hakka|min(?:\-nan)?|xiang))';
	
	/** <samp>script</samp> ABNF regular expression. */
	public const SCRIPT = '(?:' . Rfc5234::ALPHA . '{4})';
	
	/** <samp>singleton</samp> ABNF regular expression. */
	public const SINGLETON = '(?:' . Rfc5234::DIGIT . '|[A-WY-Za-wy-z])';
	
	/** <samp>variant</samp> ABNF regular expression. */
	public const VARIANT = '(?:' . self::ALPHANUM . '{5,8}|' . Rfc5234::DIGIT . self::ALPHANUM . '{3})';
}
