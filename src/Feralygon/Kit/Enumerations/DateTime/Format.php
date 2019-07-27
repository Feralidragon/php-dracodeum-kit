<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\DateTime;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents date and time formats, to be used in the PHP <code>date</code> function or similar.
 * 
 * @see https://php.net/manual/en/function.date.php
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see https://en.wikipedia.org/wiki/Unix_time
 * @see https://tools.ietf.org/html/rfc7231#section-7.1.1.1
 */
class Format extends Enumeration
{
	//Public constants
	/** ISO 8601 format. */
	public const ISO8601 = self::ISO8601_DATE . '\T' . self::ISO8601_TIME;
	
	/** ISO 8601 basic format. */
	public const ISO8601_BASIC = self::ISO8601_BASIC_DATE . '\T' . self::ISO8601_BASIC_TIME;
	
	/** ISO 8601 basic date format. */
	public const ISO8601_BASIC_DATE = 'Ymd';
	
	/** ISO 8601 basic time format. */
	public const ISO8601_BASIC_TIME = 'HisO';
	
	/** ISO 8601 date format. */
	public const ISO8601_DATE = 'Y-m-d';
	
	/** ISO 8601 time format. */
	public const ISO8601_TIME = 'H:i:sP';
	
	/** RFC 7231 <samp>HTTP-date</samp> format. */
	public const RFC7231_HTTP_DATE = 'D, d M Y H:i:s \G\M\T';
	
	/** Unix format. */
	public const UNIX = 'U';
}
