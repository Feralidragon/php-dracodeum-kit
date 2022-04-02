<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
	/** <samp>ALPHA</samp> */
	public const ALPHA = '[A-Za-z]';
	
	/** <samp>BIT</samp> */
	public const BIT = '[01]';
	
	/** <samp>CHAR</samp> */
	public const CHAR = '[\x01-\x7f]';
	
	/** <samp>CR</samp> */
	public const CR = '\r';
	
	/** <samp>CRLF</samp> */
	public const CRLF = '(?:' . self::CR . self::LF . ')';
	
	/** <samp>CTL</samp> */
	public const CTL = '[\x00-\x1f\x7f]';
	
	/** <samp>DIGIT</samp> */
	public const DIGIT = '[0-9]';
	
	/** <samp>DQUOTE</samp> */
	public const DQUOTE = '\"';
	
	/** <samp>HEXDIG</samp> */
	public const HEXDIG = '(?:' . self::DIGIT . '|[A-F])';
	
	/** <samp>HTAB</samp> */
	public const HTAB = '\t';
	
	/** <samp>LF</samp> */
	public const LF = '\n';
	
	/** <samp>LWSP</samp> */
	public const LWSP = '(?:' . self::CRLF . '?' . self::WSP . ')*';
	
	/** <samp>OCTET</samp> */
	public const OCTET = '[\x00-\xff]';
	
	/** <samp>SP</samp> */
	public const SP = '\ ';
	
	/** <samp>VCHAR</samp> */
	public const VCHAR = '[\x21-\x7e]';
	
	/** <samp>WSP</samp> */
	public const WSP = '(?:' . self::SP . '|' . self::HTAB . ')';
}
