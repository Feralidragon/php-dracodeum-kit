<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 6266 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc6266#section-4.1
 */
class Rfc6266 extends Enumeration
{
	//Public constants
	/** <samp>Content-Disposition</samp> */
	public const CONTENT_DISPOSITION = '(?:' . self::DISPOSITION_TYPE . '(?:\;' . self::DISPOSITION_PARM . ')*)';
	
	/** <samp>disp-ext-parm</samp> */
	public const DISP_EXT_PARM = '(?:' . 
		self::TOKEN . '\=' . self::VALUE . '|' . self::EXT_TOKEN . '\=' . self::EXT_VALUE . 
		')';
	
	/** <samp>disp-ext-type</samp> */
	public const DISP_EXT_TYPE = self::TOKEN;
	
	/** <samp>disposition-parm</samp> */
	public const DISPOSITION_PARM = '(?:' . self::FILENAME_PARM . '|' . self::DISP_EXT_PARM . ')';
	
	/** <samp>disposition-type</samp> */
	public const DISPOSITION_TYPE = '(?:(?i)inline|attachment|' . self::DISP_EXT_TYPE . '(?-i))';
	
	/** <samp>ext-token</samp> */
	public const EXT_TOKEN = '(?:' . self::TOKEN . '\*)';
	
	/** <samp>ext-value</samp> */
	public const EXT_VALUE = Rfc8187::EXT_VALUE;
	
	/** <samp>filename-parm</samp> */
	public const FILENAME_PARM = '(?:filename\=' . self::VALUE . '|filename\*\=' . self::EXT_VALUE . ')';
	
	/** <samp>quoted-string</samp> */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>token</samp> */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>value</samp> */
	public const VALUE = '(?:' . self::TOKEN . '|' . self::QUOTED_STRING . ')';
}
