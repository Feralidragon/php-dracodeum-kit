<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	/** <samp>entity-tag</samp> ABNF regular expression. */
	public const ENTITY_TAG = '(?:' . self::WEAK . '?' . self::OPAQUE_TAG . ')';
	
	/** <samp>ETag</samp> ABNF regular expression. */
	public const ETAG = self::ENTITY_TAG;
	
	/** <samp>etagc</samp> ABNF regular expression. */
	public const ETAGC = '[\!\x23-\x7e]';
	
	/** <samp>HTTP-date</samp> ABNF regular expression. */
	public const HTTP_DATE = Rfc7231::HTTP_DATE;
	
	/** <samp>If-Match</samp> ABNF regular expression. */
	public const IF_MATCH = '(?:\*|' . 
		'(?:,' . self::OWS . ')*' . self::ENTITY_TAG . 
		'(?:' . self::OWS . ',(?:' . self::OWS . self::ENTITY_TAG . ')?)*' . 
		')';
	
	/** <samp>If-Modified-Since</samp> ABNF regular expression. */
	public const IF_MODIFIED_SINCE = self::HTTP_DATE;
	
	/** <samp>If-None-Match</samp> ABNF regular expression. */
	public const IF_NONE_MATCH = '(?:\*|' . 
		'(?:,' . self::OWS . ')*' . self::ENTITY_TAG . 
		'(?:' . self::OWS . ',(?:' . self::OWS . self::ENTITY_TAG . ')?)*' . 
		')';
	
	/** <samp>If-Unmodified-Since</samp> ABNF regular expression. */
	public const IF_UNMODIFIED_SINCE = self::HTTP_DATE;
	
	/** <samp>Last-Modified</samp> ABNF regular expression. */
	public const LAST_MODIFIED = self::HTTP_DATE;
	
	/** <samp>opaque-tag</samp> ABNF regular expression. */
	public const OPAQUE_TAG = '(?:' . Rfc5234::DQUOTE . self::ETAGC . '*' . Rfc5234::DQUOTE . ')';
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>weak</samp> ABNF regular expression. */
	public const WEAK = '(?:W\/)';
}
