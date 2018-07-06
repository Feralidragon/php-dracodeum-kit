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
 */
class Pattern extends Enumeration
{
	//Public constants
	/** LANGUAGE-RANGE regular expression pattern. */
	public const LANGUAGE_RANGE = '(?:(?:[A-z]{1,8}(?:-[A-z\d]{1,8})*)|\*)';
	
	/** EXTENDED-LANGUAGE-RANGE regular expression pattern. */
	public const EXTENDED_LANGUAGE_RANGE = '(?:(?:[A-z]{1,8}|\*)(?:-(?:[A-z\d]{1,8}|\*))*)';
}
