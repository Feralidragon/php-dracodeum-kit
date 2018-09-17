<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 7230 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7230#appendix-B
 */
class Rfc7230 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> ABNF regular expression. */
	public const ABSOLUTE_URI = Rfc3986::ABSOLUTE_URI;
	
	/** <samp>absolute-form</samp> ABNF regular expression. */
	public const ABSOLUTE_FORM = self::ABSOLUTE_URI;
	
	/** <samp>absolute-path</samp> ABNF regular expression. */
	//public const ABSOLUTE_PATH = 1*( "/" segment )
	
	/** <samp>asterisk-form</samp> ABNF regular expression. */
	public const ASTERISK_FORM = '\*';
	
	/** <samp>authority</samp> ABNF regular expression. */
	public const AUTHORITY = Rfc3986::AUTHORITY;
	
	/** <samp>authority-form</samp> ABNF regular expression. */
	public const AUTHORITY_FORM = self::AUTHORITY;
	
	/** <samp>chunk</samp> ABNF regular expression. */
	public const CHUNK = '(?:' . self::CHUNK_SIZE . self::CHUNK_EXT . '?\r\n' . self::CHUNK_DATA . '\r\n)';
	
	/** <samp>chunk-data</samp> ABNF regular expression. */
	public const CHUNK_DATA = '(?:' . Rfc5234::OCTET . '+)';
	
	/** <samp>chunk-ext</samp> ABNF regular expression. */
	public const CHUNK_EXT = '(?:(\;' . self::CHUNK_EXT_NAME . '(?:\=' . self::CHUNK_EXT_VAL . ')?)*)';
	
	/** <samp>chunk-ext-name</samp> ABNF regular expression. */
	public const CHUNK_EXT_NAME = self::TOKEN;
	
	/** <samp>chunk-ext-val</samp> ABNF regular expression. */
	public const CHUNK_EXT_VAL = '(?:' . self::TOKEN . '|' . self::QUOTED_STRING . ')';
	
	/** <samp>chunk-size</samp> ABNF regular expression. */
	public const CHUNK_SIZE = '(?:' . Rfc5234::HEXDIG . '+)';
	
	/** <samp>chunked-body</samp> ABNF regular expression. */
	//public const CHUNKED_BODY = *chunk last-chunk trailer-part CRLF
	
	/** <samp>comment</samp> ABNF regular expression. */
	public const COMMENT = '(?:\((?:' . self::CTEXT . '|' . self::QUOTED_PAIR . ')*\))';
	
	/** <samp>Connection</samp> ABNF regular expression. */
	//public const CONNECTION = *( "," OWS ) connection-option *( OWS "," [ OWS connection-option ] )
	
	/** <samp>connection-option</samp> ABNF regular expression. */
	public const CONNECTION_OPTION = self::TOKEN;
	
	/** <samp>Content-Length</samp> ABNF regular expression. */
	public const CONTENT_LENGTH = '(?:\d+)';
	
	/** <samp>ctext</samp> ABNF regular expression. */
	public const CTEXT = '[\t\ \x21-\x27\x2a-\x5b\x5d-\x7e]';
	
	/** <samp>field-content</samp> ABNF regular expression. */
	public const FIELD_CONTENT = '(?:' . self::FIELD_VCHAR . '(?:[\ \t]+' . self::FIELD_VCHAR . ')?)';
	
	/** <samp>field-name</samp> ABNF regular expression. */
	public const FIELD_NAME = self::TOKEN;
	
	/** <samp>field-value</samp> ABNF regular expression. */
	public const FIELD_VALUE = '(?:' . self::FIELD_CONTENT . '*)';
	
	/** <samp>field-vchar</samp> ABNF regular expression. */
	public const FIELD_VCHAR = Rfc5234::VCHAR;
	
	/** <samp>fragment</samp> ABNF regular expression. */
	public const FRAGMENT = Rfc3986::FRAGMENT;
	
	/** <samp>header-field</samp> ABNF regular expression. */
	public const HEADER_FIELD = '(?:' . self::FIELD_NAME . '\:' . self::OWS . self::FIELD_VALUE . self::OWS . ')';
	
	/** <samp>Host</samp> ABNF regular expression. */
	//public const HOST = uri-host [ ":" port ]
	
	/** <samp>HTTP-message</samp> ABNF regular expression. */
	//public const HTTP_MESSAGE = start-line *( header-field CRLF ) CRLF [ message-body ]
	
	/** <samp>HTTP-name</samp> ABNF regular expression. */
	public const HTTP_NAME = '(?:HTTP)';
	
	/** <samp>http-URI</samp> ABNF regular expression. */
	//public const HTTP_URI = "http://" authority path-abempty [ "?" query ] [ "#" fragment ]
	
	/** <samp>HTTP-version</samp> ABNF regular expression. */
	public const HTTP_VERSION = '(?:' . self::HTTP_NAME . '\/\d\.\d)';
	
	/** <samp>https-URI</samp> ABNF regular expression. */
	//public const HTTPS_URI = "https://" authority path-abempty [ "?" query ] [ "#" fragment ]
	
	/** <samp>last-chunk</samp> ABNF regular expression. */
	public const LAST_CHUNK = '(?:0+' . self::CHUNK_EXT . '?\r\n)';
	
	/** <samp>message-body</samp> ABNF regular expression. */
	public const MESSAGE_BODY = '(?:' . Rfc5234::OCTET . '*)';
	
	/** <samp>method</samp> ABNF regular expression. */
	public const METHOD = self::TOKEN;
	
	/** <samp>origin-form</samp> ABNF regular expression. */
	//public const ORIGIN_FORM = absolute-path [ "?" query ]
	
	
	
	//TODO
	
	
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = '(?:[\ \t]*)';
	
	/** <samp>parameter</samp> ABNF regular expression. */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>qdtext</samp> ABNF regular expression. */
	public const QDTEXT = '[\t\ \!\x23-\x5b\x5d-\x7e]';
	
	/** <samp>quoted-pair</samp> ABNF regular expression. */
	public const QUOTED_PAIR = '(?:\\\\[\t\ \x21-\x7e])';
	
	/** <samp>quoted-string</samp> ABNF regular expression. */
	public const QUOTED_STRING = '(?:\"(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*\")';
	
	/** <samp>qvalue</samp> ABNF regular expression. */
	public const QVALUE = '(?:0(?:\.\d{0,3})?|1(?:\.0{0,3})?)';
	
	/** <samp>RWS</samp> ABNF regular expression. */
	public const RWS = '(?:[\ \t]+)';
	
	/** <samp>tchar</samp> ABNF regular expression. */
	public const TCHAR = '[\!\#\$\%\&\\\'\*\+\-\.\^\`\|\~\w]';
	
	/** <samp>TE</samp> ABNF regular expression. */
	//public const TE = [ ( "," / t-codings ) *( OWS "," [ OWS t-codings ] ) ]
	
	/** <samp>token</samp> ABNF regular expression. */
	public const TOKEN = '(?:' . self::TCHAR . '+)';
	
	/** <samp>Trailer</samp> ABNF regular expression. */
	//public const TRAILER = *( "," OWS ) field-name *( OWS "," [ OWS field-name ] )
	
	/** <samp>Transfer-Encoding</samp> ABNF regular expression. */
	//public const TRANSFER_ENCODING = *( "," OWS ) transfer-coding *( OWS "," [ OWS transfer-coding ] )
	
	/** <samp>URI-reference</samp> ABNF regular expression. */
	public const URI_REFERENCE = Rfc3986::URI_REFERENCE;
	
	/** <samp>Upgrade</samp> ABNF regular expression. */
	//public const UPGRADE = *( "," OWS ) protocol *( OWS "," [ OWS protocol ] )
	
	/** <samp>Via</samp> ABNF regular expression. */
	//public const VIA = *( "," OWS ) ( received-protocol RWS received-by [ RWS comment ] ) *( OWS "," [ OWS ( received-protocol RWS received-by [ RWS comment ] ) ] )
	
	/** <samp>weight</samp> ABNF regular expression. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . '[Qq]\=' . self::QVALUE . ')';
}
