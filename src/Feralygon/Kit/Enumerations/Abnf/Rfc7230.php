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
	public const ABSOLUTE_PATH = '(?:(?:\/' . self::SEGMENT . ')+)';
	
	/** <samp>asterisk-form</samp> ABNF regular expression. */
	public const ASTERISK_FORM = '\*';
	
	/** <samp>authority</samp> ABNF regular expression. */
	public const AUTHORITY = Rfc3986::AUTHORITY;
	
	/** <samp>authority-form</samp> ABNF regular expression. */
	public const AUTHORITY_FORM = self::AUTHORITY;
	
	/** <samp>chunk</samp> ABNF regular expression. */
	public const CHUNK = '(?:' . 
		self::CHUNK_SIZE . self::CHUNK_EXT . '?' . Rfc5234::CRLF . self::CHUNK_DATA . Rfc5234::CRLF . 
		')';
	
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
	public const CHUNKED_BODY = '(?:' . self::CHUNK . '*' . self::LAST_CHUNK . self::TRAILER_PART . Rfc5234::CRLF . ')';
	
	/** <samp>comment</samp> ABNF regular expression. */
	public const COMMENT = '(\((?:' . self::CTEXT . '|' . self::QUOTED_PAIR . '|(?-1))*\))';
	
	/** <samp>Connection</samp> ABNF regular expression. */
	public const CONNECTION = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::CONNECTION_OPTION . '(?:' . self::OWS . '\,(?:' . self::OWS . self::CONNECTION_OPTION . ')?)*' . 
		')';
	
	/** <samp>connection-option</samp> ABNF regular expression. */
	public const CONNECTION_OPTION = self::TOKEN;
	
	/** <samp>Content-Length</samp> ABNF regular expression. */
	public const CONTENT_LENGTH = '(?:' . Rfc5234::DIGIT . '+)';
	
	/** <samp>ctext</samp> ABNF regular expression. */
	public const CTEXT = '(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|[\x21-\x27\x2a-\x5b\x5d-\x7e])';
	
	/** <samp>field-content</samp> ABNF regular expression. */
	public const FIELD_CONTENT = '(?:' . 
		self::FIELD_VCHAR . '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')+' . self::FIELD_VCHAR . ')?' . 
		')';
	
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
	public const HOST = '(?:' . self::URI_HOST . '(?:\:' . self::PORT . ')?)';
	
	/** <samp>HTTP-message</samp> ABNF regular expression. */
	public const HTTP_MESSAGE = '(?:' . 
		self::START_LINE . 
		'(?:' . self::HEADER_FIELD . Rfc5234::CRLF . ')*' . 
		Rfc5234::CRLF . self::MESSAGE_BODY . '?' . 
		')';
	
	/** <samp>HTTP-name</samp> ABNF regular expression. */
	public const HTTP_NAME = '(?:HTTP)';
	
	/** <samp>http-URI</samp> ABNF regular expression. */
	public const HTTP_URI = '(?:http\:\/\/' . self::AUTHORITY . self::PATH_ABEMPTY . '(?:\?' . self::QUERY . ')?' . 
		'(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>HTTP-version</samp> ABNF regular expression. */
	public const HTTP_VERSION = '(?:' . self::HTTP_NAME . '\/' . Rfc5234::DIGIT . '\.' . Rfc5234::DIGIT . ')';
	
	/** <samp>https-URI</samp> ABNF regular expression. */
	public const HTTPS_URI = '(?:https\:\/\/' . self::AUTHORITY . self::PATH_ABEMPTY . '(?:\?' . self::QUERY . ')?' . 
		'(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>last-chunk</samp> ABNF regular expression. */
	public const LAST_CHUNK = '(?:0+' . self::CHUNK_EXT . '?' . Rfc5234::CRLF . ')';
	
	/** <samp>message-body</samp> ABNF regular expression. */
	public const MESSAGE_BODY = '(?:' . Rfc5234::OCTET . '*)';
	
	/** <samp>method</samp> ABNF regular expression. */
	public const METHOD = self::TOKEN;
	
	/** <samp>origin-form</samp> ABNF regular expression. */
	public const ORIGIN_FORM = '(?:' . self::ABSOLUTE_PATH . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')*)';
	
	/** <samp>parameter</samp> ABNF regular expression. */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>partial-URI</samp> ABNF regular expression. */
	public const PARTIAL_URI = '(?:' . self::RELATIVE_PART . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>path-abempty</samp> ABNF regular expression. */
	public const PATH_ABEMPTY = Rfc3986::PATH_ABEMPTY;
	
	/** <samp>port</samp> ABNF regular expression. */
	public const PORT = Rfc3986::PORT;
	
	/** <samp>protocol</samp> ABNF regular expression. */
	public const PROTOCOL = '(?:' . self::PROTOCOL_NAME . '(?:\/' . self::PROTOCOL_VERSION . ')?)';
	
	/** <samp>protocol-name</samp> ABNF regular expression. */
	public const PROTOCOL_NAME = self::TOKEN;
	
	/** <samp>protocol-version</samp> ABNF regular expression. */
	public const PROTOCOL_VERSION = self::TOKEN;
	
	/** <samp>pseudonym</samp> ABNF regular expression. */
	public const PSEUDONYM = self::TOKEN;
	
	/** <samp>qdtext</samp> ABNF regular expression. */
	public const QDTEXT = '(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|[\!\x23-\x5b\x5d-\x7e])';
	
	/** <samp>query</samp> ABNF regular expression. */
	public const QUERY = Rfc3986::QUERY;
	
	/** <samp>quoted-pair</samp> ABNF regular expression. */
	public const QUOTED_PAIR = '(?:\\\\(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|' . Rfc5234::VCHAR . '))';
	
	/** <samp>quoted-string</samp> ABNF regular expression. */
	public const QUOTED_STRING = '(?:' . 
		Rfc5234::DQUOTE . '(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*' . Rfc5234::DQUOTE . 
		')';
	
	/** <samp>rank</samp> ABNF regular expression. */
	public const RANK = '(?:(?:0(?:\.' . Rfc5234::DIGIT . '{0,3})?)|(?:1(?:\.0{0,3})?))';
	
	/** <samp>reason-phrase</samp> ABNF regular expression. */
	public const REASON_PHRASE = '(?:(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|' . Rfc5234::VCHAR . ')*)';
	
	/** <samp>received-by</samp> ABNF regular expression. */
	public const RECEIVED_BY = '(?:' . self::URI_HOST . '(?:\:' . self::PORT . ')?|' . self::PSEUDONYM . ')';
	
	/** <samp>received-protocol</samp> ABNF regular expression. */
	public const RECEIVED_PROTOCOL = '(?:(?:' . self::PROTOCOL_NAME . '\/)?' . self::PROTOCOL_VERSION . ')';
	
	/** <samp>relative-part</samp> ABNF regular expression. */
	public const RELATIVE_PART = Rfc3986::RELATIVE_PART;
	
	/** <samp>request-line</samp> ABNF regular expression. */
	public const REQUEST_LINE = '(?:' . 
		self::METHOD . Rfc5234::SP . self::REQUEST_TARGET . Rfc5234::SP . self::HTTP_VERSION . Rfc5234::CRLF . 
		')';
	
	/** <samp>request-target</samp> ABNF regular expression. */
	public const REQUEST_TARGET = '(?:' . 
		self::ORIGIN_FORM . '|' . self::ABSOLUTE_FORM . '|' . self::AUTHORITY_FORM . '|' . self::ASTERISK_FORM . 
		')';
	
	/** <samp>RWS</samp> ABNF regular expression. */
	public const RWS = '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')+)';
	
	/** <samp>scheme</samp> ABNF regular expression. */
	public const SCHEME = Rfc3986::SCHEME;
	
	/** <samp>segment</samp> ABNF regular expression. */
	public const SEGMENT = Rfc3986::SEGMENT;
	
	/** <samp>start-line</samp> ABNF regular expression. */
	public const START_LINE = '(?:' . self::REQUEST_LINE . '|' . self::STATUS_LINE . ')';
	
	/** <samp>status-code</samp> ABNF regular expression. */
	public const STATUS_CODE = '(?:' . Rfc5234::DIGIT . '{3})';
	
	/** <samp>status-line</samp> ABNF regular expression. */
	public const STATUS_LINE = '(?:' . 
		self::HTTP_VERSION . Rfc5234::SP . self::STATUS_CODE . Rfc5234::SP . self::REASON_PHRASE . Rfc5234::CRLF . 
		')';
	
	/** <samp>t-codings</samp> ABNF regular expression. */
	public const T_CODINGS = '(?:trailers|' . self::TRANSFER_CODING . self::T_RANKING . '?)';
	
	/** <samp>t-ranking</samp> ABNF regular expression. */
	public const T_RANKING = '(?:' . self::OWS . '\;' . self::OWS . 'q\=' . self::RANK . ')';
	
	/** <samp>tchar</samp> ABNF regular expression. */
	public const TCHAR = '(?:[\!\#\$\%\&\\\'\*\+\-\.\^\_\`\|\~]|' . Rfc5234::DIGIT . '|' . Rfc5234::ALPHA . ')';
	
	/** <samp>TE</samp> ABNF regular expression. */
	public const TE = '(?:' . 
		'(?:(?:\,|' . self::T_CODINGS . ')(?:' . self::OWS . '\,(?:' . self::OWS . self::T_CODINGS . ')?)*)?' . 
		')';
	
	/** <samp>token</samp> ABNF regular expression. */
	public const TOKEN = '(?:' . self::TCHAR . '+)';
	
	/** <samp>Trailer</samp> ABNF regular expression. */
	public const TRAILER = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::FIELD_NAME . '(?:' . self::OWS . '\,(?:' . self::OWS . self::FIELD_NAME . ')?)*' . 
		')';
	
	/** <samp>trailer-part</samp> ABNF regular expression. */
	public const TRAILER_PART = '(?:(?:' . self::HEADER_FIELD . Rfc5234::CRLF . ')*)';
	
	/** <samp>transfer-coding</samp> ABNF regular expression. */
	public const TRANSFER_CODING = '(?:chunked|compress|deflate|gzip|' . self::TRANSFER_EXTENSION . ')';
	
	/** <samp>Transfer-Encoding</samp> ABNF regular expression. */
	public const TRANSFER_ENCODING = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::TRANSFER_CODING . '(?:' . self::OWS . '\,(?:' . self::OWS . self::TRANSFER_CODING . ')?)*' . 
		')';
	
	/** <samp>transfer-extension</samp> ABNF regular expression. */
	public const TRANSFER_EXTENSION = '(?:' . self::TOKEN . '(?:' . self::OWS . '\;' . self::OWS . 
		self::TRANSFER_PARAMETER . ')*)';
	
	/** <samp>transfer-parameter</samp> ABNF regular expression. */
	public const TRANSFER_PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>URI-reference</samp> ABNF regular expression. */
	public const URI_REFERENCE = Rfc3986::URI_REFERENCE;
	
	/** <samp>uri-host</samp> ABNF regular expression. */
	public const URI_HOST = Rfc3986::HOST;
	
	/** <samp>Upgrade</samp> ABNF regular expression. */
	public const UPGRADE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::PROTOCOL . '(?:' . self::OWS . '\,(?:' . self::OWS . self::PROTOCOL . ')?)*' . 
		')';
	
	/** <samp>Via</samp> ABNF regular expression. */
	public const VIA = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::RECEIVED_PROTOCOL . self::RWS . self::RECEIVED_BY . '(?:' . self::RWS . self::COMMENT . ')?' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::RECEIVED_PROTOCOL . self::RWS . self::RECEIVED_BY . 
			'(?:' . self::RWS . self::COMMENT . ')?)?)*' . 
		')';
}
