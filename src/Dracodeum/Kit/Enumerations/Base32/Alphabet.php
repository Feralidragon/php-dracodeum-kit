<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Base32;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents Base32 alphabets.
 * 
 * @see https://en.wikipedia.org/wiki/Base32
 * @see https://en.wikipedia.org/wiki/Geohash
 * @see https://tools.ietf.org/html/rfc4648
 */
class Alphabet extends Enumeration
{
	//Public constants
	/** RFC 4648. */
	public const RFC4648 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
	
	/** z-base-32. */
	public const ZBASE32 = 'ybndrfg8ejkmcpqxot1uwisza345h769';
	
	/** Geohash. */
	public const GEOHASH = '0123456789bcdefghjkmnpqrstuvwxyz';
}
