<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 5234 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc5234#appendix-B.1
 */
class Rfc5234 extends Enumeration
{
	//Public constants
	/** <samp>ALPHA</samp> ABNF regular expression. */
	public const ALPHA = '[A-Za-z]';
	
	/** <samp>BIT</samp> ABNF regular expression. */
	public const BIT = '[01]';
	
	/** <samp>CHAR</samp> ABNF regular expression. */
	public const CHAR = '[\x01-\x7f]';
	
	/** <samp>CR</samp> ABNF regular expression. */
	public const CR = '\r';
	
	/** <samp>CRLF</samp> ABNF regular expression. */
	public const CRLF = '(?:' . self::CR . self::LF . ')';
	
	/** <samp>CTL</samp> ABNF regular expression. */
	public const CTL = '[\x00-\x1f\x7f]';
	
	/** <samp>DIGIT</samp> ABNF regular expression. */
	public const DIGIT = '[0-9]';
	
	/** <samp>DQUOTE</samp> ABNF regular expression. */
	public const DQUOTE = '\"';
	
	/** <samp>HEXDIG</samp> ABNF regular expression. */
	public const HEXDIG = '(?:' . self::DIGIT . '|[A-F])';
	
	/** <samp>HTAB</samp> ABNF regular expression. */
	public const HTAB = '\t';
	
	/** <samp>LF</samp> ABNF regular expression. */
	public const LF = '\n';
	
	/** <samp>LWSP</samp> ABNF regular expression. */
	public const LWSP = '(?:' . self::CRLF . '?' . self::WSP . ')*';
	
	/** <samp>OCTET</samp> ABNF regular expression. */
	public const OCTET = '[\x00-\xff]';
	
	/** <samp>SP</samp> ABNF regular expression. */
	public const SP = '\ ';
	
	/** <samp>VCHAR</samp> ABNF regular expression. */
	public const VCHAR = '[\x21-\x7e]';
	
	/** <samp>WSP</samp> ABNF regular expression. */
	public const WSP = '(?:' . self::SP . '|' . self::HTAB . ')';
}
