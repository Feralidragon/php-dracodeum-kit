<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	/** <samp>attr-char</samp> ABNF regular expression. */
	public const ATTR_CHAR = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\!\#\$\&\+\-\.\^\_\`\|\~])';
	
	/** <samp>charset</samp> ABNF regular expression. */
	public const CHARSET = '(?:UTF\-8|' . self::MIME_CHARSET . ')';
	
	/** <samp>ext-value</samp> ABNF regular expression. */
	public const EXT_VALUE = '(?:' . self::CHARSET . '\\\'' . self::LANGUAGE . '?\\\'' . self::VALUE_CHARS . ')';
	
	/** <samp>language</samp> ABNF regular expression. */
	public const LANGUAGE = Rfc5646::LANGUAGE_TAG;
	
	/** <samp>mime-charset</samp> ABNF regular expression. */
	public const MIME_CHARSET = '(?:' . self::MIME_CHARSETC . '+)';
	
	/** <samp>mime-charsetc</samp> ABNF regular expression. */
	public const MIME_CHARSETC = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\!\#\$\%\&\+\-\^\_\`\{\}\~])';
	
	/** <samp>pct-encoded</samp> ABNF regular expression. */
	public const PCT_ENCODED = '(?:\%' . Rfc5234::HEXDIG . '{2})';
	
	/** <samp>value-chars</samp> ABNF regular expression. */
	public const VALUE_CHARS = '(?:(?:' . self::PCT_ENCODED . '|' . self::ATTR_CHAR . ')*)';
}
