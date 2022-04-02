<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 8187 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc8187#section-3.2.1
 */
class Rfc8187 extends Enumeration
{
	//Public constants
	/** <samp>attr-char</samp> */
	public const ATTR_CHAR = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\!\#\$\&\+\-\.\^\_\`\|\~])';
	
	/** <samp>charset</samp> */
	public const CHARSET = '(?:UTF\-8|' . self::MIME_CHARSET . ')';
	
	/** <samp>ext-value</samp> */
	public const EXT_VALUE = '(?:' . self::CHARSET . '\\\'' . self::LANGUAGE . '?\\\'' . self::VALUE_CHARS . ')';
	
	/** <samp>language</samp> */
	public const LANGUAGE = Rfc5646::LANGUAGE_TAG;
	
	/** <samp>mime-charset</samp> */
	public const MIME_CHARSET = '(?:' . self::MIME_CHARSETC . '+)';
	
	/** <samp>mime-charsetc</samp> */
	public const MIME_CHARSETC = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\!\#\$\%\&\+\-\^\_\`\{\}\~])';
	
	/** <samp>pct-encoded</samp> */
	public const PCT_ENCODED = '(?:\%' . Rfc5234::HEXDIG . '{2})';
	
	/** <samp>value-chars</samp> */
	public const VALUE_CHARS = '(?:(?:' . self::PCT_ENCODED . '|' . self::ATTR_CHAR . ')*)';
}
