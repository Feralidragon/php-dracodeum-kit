<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 7231 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7231#appendix-C
 * @see https://tools.ietf.org/html/rfc7231#appendix-D
 */
class Rfc7231 extends Enumeration
{
	//Public constants
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>qvalue</samp> ABNF regular expression. */
	public const QVALUE = '(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)';
	
	/** <samp>weight</samp> ABNF regular expression. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . '[Qq]\=' . self::QVALUE . ')';
}
