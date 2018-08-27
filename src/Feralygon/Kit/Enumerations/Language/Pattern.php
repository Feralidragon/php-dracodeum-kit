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
	/** EXTLANG regular expression pattern. */
	public const EXTLANG = '(?:[A-Za-z]{3}(?:-[A-Za-z]{3}){0,2})';
	
	/** LANGUAGE regular expression pattern. */
	public const LANGUAGE = '(?:[A-Za-z]{2,3}(?:-' . self::EXTLANG . ')?|[A-Za-z]{4,8})';
	
	/** LANGUAGE-RANGE regular expression pattern. */
	public const LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}(?:-[A-Za-z\d]{1,8})*)|\*)';
	
	/** EXTENDED-LANGUAGE-RANGE regular expression pattern. */
	public const EXTENDED_LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}|\*)(?:-(?:[A-Za-z\d]{1,8}|\*))*)';
	
	/** SCRIPT regular expression pattern. */
	public const SCRIPT = '(?:[A-Za-z]{4})';
	
	/** REGION regular expression pattern. */
	public const REGION = '(?:[A-Za-z]{2}|\d{3})';
	
	/** VARIANT regular expression pattern. */
	public const VARIANT = '(?:[A-Za-z]{5-8}|\d[A-Za-z\d]{3})';
	
	/** SINGLETON regular expression pattern. */
	public const SINGLETON = '[\dA-WY-Za-wy-z]';
	
	/** PRIVATEUSE regular expression pattern. */
	public const PRIVATEUSE = '(?:x(?:-[A-Za-z\d]{1,8})+)';
	
	/** EXTENSION regular expression pattern. */
	public const EXTENSION = '(?:' . self::SINGLETON . '(?:-[A-Za-z\d]{2,8})+)';
	
	/** LANGTAG regular expression pattern. */
	public const LANGTAG = '(?:' . self::LANGUAGE . '(?:-' . self::SCRIPT . ')?(?:-' . self::REGION . ')?' . 
		'(?:-' . self::VARIANT . ')*(?:-' . self::EXTENSION . ')*(?:-' . self::PRIVATEUSE . ')?)';
	
	/** IRREGULAR regular expression pattern. */
	public const IRREGULAR = '(?:' . 
		'en-GB-oed|' . 
		'i-(?:ami|bnn|default|enochian|hak|klingon|lux|mingo|navajo|pwn|tao|tay|tsu)|' . 
		'sgn-(?:BE(?:FR|NL)|CH-DE)' . 
		')';
	
	/** REGULAR regular expression pattern. */
	public const REGULAR = '(?:art-lojban|cel-gaulish|no-(?:bok|nyn)|zh-(?:guoyu|hakka|min(?:-nan)?|xiang))';
	
	/** GRANDFATHERED regular expression pattern. */
	public const GRANDFATHERED = '(?:' . self::IRREGULAR . '|' . self::REGULAR . ')';
	
	/** LANGUAGE-TAG regular expression pattern. */
	public const LANGUAGE_TAG = '(?:' . self::LANGTAG . '|' . self::PRIVATEUSE . '|' . self::GRANDFATHERED . ')';
}
