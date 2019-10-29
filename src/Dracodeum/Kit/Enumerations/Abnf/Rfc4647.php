<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 4647 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc4647#section-2.1
 * @see https://tools.ietf.org/html/rfc4647#section-2.2
 */
class Rfc4647 extends Enumeration
{
	//Public constants
	/** <samp>alphanum</samp> ABNF regular expression. */
	public const ALPHANUM = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . ')';
	
	/** <samp>extended-language-range</samp> ABNF regular expression. */
	public const EXTENDED_LANGUAGE_RANGE = '(?:' . 
		'(?:' . Rfc5234::ALPHA . '{1,8}|\*)(?:\-(?:' . self::ALPHANUM . '{1,8}|\*))*' . 
		')';
	
	/** <samp>language-range</samp> ABNF regular expression. */
	public const LANGUAGE_RANGE = '(?:(?:' . Rfc5234::ALPHA . '{1,8}(?:\-' . self::ALPHANUM . '{1,8})*)|\*)';
}
