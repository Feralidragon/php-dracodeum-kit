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
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7235#appendix-C
 */
class Rfc7235 extends Enumeration
{
	//Public constants
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>quoted-string</samp> ABNF regular expression. */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>token</samp> ABNF regular expression. */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>token68</samp> ABNF regular expression. */
	public const TOKEN68 = '(?:(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\-\.\_\~\+\/])+\=*)';
}
