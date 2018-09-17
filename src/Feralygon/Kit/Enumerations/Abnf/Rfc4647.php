<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 4647 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc4647#section-2.1
 * @see https://tools.ietf.org/html/rfc4647#section-2.2
 */
class Rfc4647 extends Enumeration
{
	//Public constants
	/** <samp>extended-language-range</samp> ABNF regular expression. */
	public const EXTENDED_LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}|\*)(?:-(?:[A-Za-z\d]{1,8}|\*))*)';
	
	/** <samp>language-range</samp> ABNF regular expression. */
	public const LANGUAGE_RANGE = '(?:(?:[A-Za-z]{1,8}(?:-[A-Za-z\d]{1,8})*)|\*)';
}
