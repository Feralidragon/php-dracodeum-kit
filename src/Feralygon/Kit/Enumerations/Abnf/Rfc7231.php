<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 7231 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7231#appendix-C
 * @see https://tools.ietf.org/html/rfc7231#appendix-D
 */
class Rfc7231 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> ABNF regular expression. */
	public const ABSOLUTE_URI = Rfc7230::ABSOLUTE_URI;
	
	/** <samp>Accept</samp> ABNF regular expression. */
	//public const ACCEPT = [ ( "," / ( media-range [ accept-params ] ) ) *( OWS "," [ OWS ( media-range [ accept-params ] ) ] ) ] //TODO
	
	/** <samp>Accept-Charset</samp> ABNF regular expression. */
	//public const ACCEPT_CHARSET = *( "," OWS ) ( ( charset / "*" ) [ weight ] ) *( OWS "," [ OWS ( ( charset / "*" ) [ weight ] ) ] ) //TODO
	
	/** <samp>Accept-Encoding</samp> ABNF regular expression. */
	//public const ACCEPT_ENCODING = [ ( "," / ( codings [ weight ] ) ) *( OWS "," [ OWS ( codings [ weight ] ) ] ) ] //TODO
	
	/** <samp>Accept-Language</samp> ABNF regular expression. */
	//public const ACCEPT_LANGUAGE = *( "," OWS ) ( language-range [ weight ] ) *( OWS "," [ OWS ( language-range [ weight ] ) ] ) //TODO
	
	/** <samp>Allow</samp> ABNF regular expression. */
	//public const ALLOW = [ ( "," / method ) *( OWS "," [ OWS method ] ) ] //TODO
	
	/** <samp>comment</samp> ABNF regular expression. */
	public const COMMENT = Rfc7230::COMMENT;
	
	/** <samp>Content-Encoding</samp> ABNF regular expression. */
	//public const CONTENT_ENCODING = *( "," OWS ) content-coding *( OWS "," [ OWS content-coding ] ) //TODO
	
	/** <samp>Content-Language</samp> ABNF regular expression. */
	//public const CONTENT_LANGUAGE = *( "," OWS ) language-tag *( OWS "," [ OWS language-tag ] ) //TODO
	
	/** <samp>Content-Location</samp> ABNF regular expression. */
	//public const CONTENT_LOCATION = absolute-URI / partial-URI //TODO
	
	/** <samp>Content-Type</samp> ABNF regular expression. */
	//public const CONTENT_TYPE = media-type //TODO
	
	/** <samp>Date</samp> ABNF regular expression. */
	//public const DATE = HTTP-date //TODO
	
	/** <samp>Expect</samp> ABNF regular expression. */
	public const EXPECT = '(?:100\-continue)';
	
	/** <samp>field-name</samp> ABNF regular expression. */
	public const FIELD_NAME = Rfc7230::FIELD_NAME;
	
	/** <samp>From</samp> ABNF regular expression. */
	//public const FROM = mailbox //TODO (depends on RFC 5322)
	
	/** <samp>GMT</samp> ABNF regular expression. */
	public const GMT = '(?:GMT)';
	
	/** <samp>HTTP-date</samp> ABNF regular expression. */
	//public const HTTP_DATE = IMF-fixdate //TODO
	
	/** <samp>IMF-fixdate</samp> ABNF regular expression. */
	//public const IMF_FIXDATE = day-name "," SP date1 SP time-of-day SP GMT //TODO
	
	/** <samp>Location</samp> ABNF regular expression. */
	public const LOCATION = self::URI_REFERENCE;
	
	/** <samp>Max-Forwards</samp> ABNF regular expression. */
	public const MAX_FORWARDS = '(?:\d+)';
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>partial-URI</samp> ABNF regular expression. */
	public const PARTIAL_URI = Rfc7230::PARTIAL_URI;
	
	/** <samp>qvalue</samp> ABNF regular expression. */
	public const QVALUE = '(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)';
	
	/** <samp>Referer</samp> ABNF regular expression. */
	//public const REFERER = absolute-URI / partial-URI //TODO
	
	/** <samp>Retry-After</samp> ABNF regular expression. */
	//public const RETRY_AFTER = HTTP-date / delay-seconds //TODO
	
	/** <samp>RWS</samp> ABNF regular expression. */
	public const RWS = Rfc7230::RWS;
	
	/** <samp>Server</samp> ABNF regular expression. */
	//public const SERVER = product *( RWS ( product / comment ) ) //TODO
	
	/** <samp>URI-reference</samp> ABNF regular expression. */
	public const URI_REFERENCE = Rfc7230::URI_REFERENCE;
	
	/** <samp>User-Agent</samp> ABNF regular expression. */
	//public const USER_AGENT = product *( RWS ( product / comment ) ) //TODO
	
	/** <samp>Vary</samp> ABNF regular expression. */
	//public const VARY = "*" / ( *( "," OWS ) field-name *( OWS "," [ OWS field-name ] ) ) //TODO
	
	/** <samp>weight</samp> ABNF regular expression. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . '[Qq]\=' . self::QVALUE . ')';
}
