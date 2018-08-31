<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents ABNF specification regular expression patterns.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc5234#appendix-B.1
 */
class Pattern extends Enumeration
{
	//Public constants
	/** <samp>ALPHA</samp> regular expression pattern. */
	public const ALPHA = '[A-Za-z]';
	
	/** <samp>BIT</samp> regular expression pattern. */
	public const BIT = '[01]';
	
	/** <samp>CHAR</samp> regular expression pattern. */
	public const CHAR = '[\x01-\x7f]';
	
	/** <samp>CR</samp> regular expression pattern. */
	public const CR = '\r';
	
	/** <samp>LF</samp> regular expression pattern. */
	public const LF = '\n';
	
	/** <samp>CRLF</samp> regular expression pattern. */
	public const CRLF = '(?:\r\n)';
	
	/** <samp>CTL</samp> regular expression pattern. */
	public const CTL = '[\x00-\x1f\x7f]';
	
	/** <samp>DIGIT</samp> regular expression pattern. */
	public const DIGIT = '\d';
	
	/** <samp>DQUOTE</samp> regular expression pattern. */
	public const DQUOTE = '\"';
	
	/** <samp>HEXDIG</samp> regular expression pattern. */
	public const HEXDIG = '[\dA-F]';
	
	/** <samp>HTAB</samp> regular expression pattern. */
	public const HTAB = '\t';
	
	/** <samp>WSP</samp> regular expression pattern. */
	public const WSP = '[\t\ ]';
	
	/** <samp>LWSP</samp> regular expression pattern. */
	public const LWSP = '(?:(?:\r\n)?[\t\ ])*';
	
	/** <samp>OCTET</samp> regular expression pattern. */
	public const OCTET = '[\x00-\xff]';
	
	/** <samp>VCHAR</samp> regular expression pattern. */
	public const VCHAR = '[\x21-\x7e]';
}
