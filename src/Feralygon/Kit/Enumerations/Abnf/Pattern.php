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
	/** ALPHA regular expression pattern. */
	public const ALPHA = '[A-z]';
	
	/** BIT regular expression pattern. */
	public const BIT = '[01]';
	
	/** CHAR regular expression pattern. */
	public const CHAR = '[\x01-\x7f]';
	
	/** CR regular expression pattern. */
	public const CR = '\r';
	
	/** LF regular expression pattern. */
	public const LF = '\n';
	
	/** CRLF regular expression pattern. */
	public const CRLF = '\r\n';
	
	/** CTL regular expression pattern. */
	public const CTL = '[\x00-\x1f\x7f]';
	
	/** DIGIT regular expression pattern. */
	public const DIGIT = '\d';
	
	/** DQUOTE regular expression pattern. */
	public const DQUOTE = '\"';
	
	/** HEXDIG regular expression pattern. */
	public const HEXDIG = '[\dA-F]';
	
	/** HTAB regular expression pattern. */
	public const HTAB = '\t';
	
	/** WSP regular expression pattern. */
	public const WSP = '[\t\ ]';
	
	/** LWSP regular expression pattern. */
	public const LWSP = '(?:(?:\r\n)?[\t\ ])*';
	
	/** OCTET regular expression pattern. */
	public const OCTET = '[\x00-\xff]';
	
	/** VCHAR regular expression pattern. */
	public const VCHAR = '[\x21-\x7e]';
}
