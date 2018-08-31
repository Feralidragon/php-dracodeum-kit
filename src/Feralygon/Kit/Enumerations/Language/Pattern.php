<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Language;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents language specification regular expression patterns.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc4647#section-2.1
 * @see https://tools.ietf.org/html/rfc4647#section-2.2
 * @see https://tools.ietf.org/html/rfc5646#section-2.1
 */
class Pattern extends Enumeration
{
	//Public constants
	/** <samp>extlang</samp> regular expression pattern. */
	public const EXTLANG = '(?:[A-Za-z]{3}(?:-[A-Za-z]{3}){0,2})';
	
	/** <samp>language</samp> regular expression pattern. */
	public const LANGUAGE = '(?:[A-Za-z]{2,3}(?:-' . self::EXTLANG . ')?|[A-Za-z]{4,8})';
	
	/** <samp>language-range</samp> regular expression pattern. */
	public const LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}(?:-[A-Za-z\d]{1,8})*)|\*)';
	
	/** <samp>extended-language-range</samp> regular expression pattern. */
	public const EXTENDED_LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}|\*)(?:-(?:[A-Za-z\d]{1,8}|\*))*)';
	
	/** <samp>script</samp> regular expression pattern. */
	public const SCRIPT = '(?:[A-Za-z]{4})';
	
	/** <samp>region</samp> regular expression pattern. */
	public const REGION = '(?:[A-Za-z]{2}|\d{3})';
	
	/** <samp>variant</samp> regular expression pattern. */
	public const VARIANT = '(?:[A-Za-z]{5-8}|\d[A-Za-z\d]{3})';
	
	/** <samp>singleton</samp> regular expression pattern. */
	public const SINGLETON = '[\dA-WY-Za-wy-z]';
	
	/** <samp>privateuse</samp> regular expression pattern. */
	public const PRIVATEUSE = '(?:x(?:-[A-Za-z\d]{1,8})+)';
	
	/** <samp>extension</samp> regular expression pattern. */
	public const EXTENSION = '(?:' . self::SINGLETON . '(?:-[A-Za-z\d]{2,8})+)';
	
	/** <samp>langtag</samp> regular expression pattern. */
	public const LANGTAG = '(?:' . self::LANGUAGE . '(?:-' . self::SCRIPT . ')?(?:-' . self::REGION . ')?' . 
		'(?:-' . self::VARIANT . ')*(?:-' . self::EXTENSION . ')*(?:-' . self::PRIVATEUSE . ')?)';
	
	/** <samp>irregular</samp> regular expression pattern. */
	public const IRREGULAR = '(?:' . 
		'en-GB-oed|' . 
		'i-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|tao|tay|tsu)|' . 
		'sgn-(?:BE(?:FR|NL)|CH-DE)' . 
		')';
	
	/** <samp>regular</samp> regular expression pattern. */
	public const REGULAR = '(?:art-lojban|cel-gaulish|no-(?:bok|nyn)|zh-(?:guoyu|hakka|min(?:-nan)?|xiang))';
	
	/** <samp>grandfathered</samp> regular expression pattern. */
	public const GRANDFATHERED = '(?:' . self::IRREGULAR . '|' . self::REGULAR . ')';
	
	/** <samp>language-tag</samp> regular expression pattern. */
	public const LANGUAGE_TAG = '(?:' . self::LANGTAG . '|' . self::PRIVATEUSE . '|' . self::GRANDFATHERED . ')';
}
