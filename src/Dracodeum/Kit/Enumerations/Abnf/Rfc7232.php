<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 7232 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc7232#appendix-C
 */
class Rfc7232 extends Enumeration
{
	//Public constants
	/** <samp>entity-tag</samp> */
	public const ENTITY_TAG = '(?:' . self::WEAK . '?' . self::OPAQUE_TAG . ')';
	
	/** <samp>ETag</samp> */
	public const ETAG = self::ENTITY_TAG;
	
	/** <samp>etagc</samp> */
	public const ETAGC = '[\!\x23-\x7e]';
	
	/** <samp>HTTP-date</samp> */
	public const HTTP_DATE = Rfc7231::HTTP_DATE;
	
	/** <samp>If-Match</samp> */
	public const IF_MATCH = '(?:\*|' . 
		'(?:,' . self::OWS . ')*' . self::ENTITY_TAG . 
		'(?:' . self::OWS . ',(?:' . self::OWS . self::ENTITY_TAG . ')?)*' . 
		')';
	
	/** <samp>If-Modified-Since</samp> */
	public const IF_MODIFIED_SINCE = self::HTTP_DATE;
	
	/** <samp>If-None-Match</samp> */
	public const IF_NONE_MATCH = '(?:\*|' . 
		'(?:,' . self::OWS . ')*' . self::ENTITY_TAG . 
		'(?:' . self::OWS . ',(?:' . self::OWS . self::ENTITY_TAG . ')?)*' . 
		')';
	
	/** <samp>If-Unmodified-Since</samp> */
	public const IF_UNMODIFIED_SINCE = self::HTTP_DATE;
	
	/** <samp>Last-Modified</samp> */
	public const LAST_MODIFIED = self::HTTP_DATE;
	
	/** <samp>opaque-tag</samp> */
	public const OPAQUE_TAG = '(?:' . Rfc5234::DQUOTE . self::ETAGC . '*' . Rfc5234::DQUOTE . ')';
	
	/** <samp>OWS</samp> */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>weak</samp> */
	public const WEAK = '(?:W\/)';
}
