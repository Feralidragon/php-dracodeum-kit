<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 7230 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc7230#appendix-B
 */
class Rfc7230 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> */
	public const ABSOLUTE_URI = Rfc3986::ABSOLUTE_URI;
	
	/** <samp>absolute-form</samp> */
	public const ABSOLUTE_FORM = self::ABSOLUTE_URI;
	
	/** <samp>absolute-path</samp> */
	public const ABSOLUTE_PATH = '(?:(?:\/' . self::SEGMENT . ')+)';
	
	/** <samp>asterisk-form</samp> */
	public const ASTERISK_FORM = '\*';
	
	/** <samp>authority</samp> */
	public const AUTHORITY = Rfc3986::AUTHORITY;
	
	/** <samp>authority-form</samp> */
	public const AUTHORITY_FORM = self::AUTHORITY;
	
	/** <samp>chunk</samp> */
	public const CHUNK = '(?:' . 
		self::CHUNK_SIZE . self::CHUNK_EXT . '?' . Rfc5234::CRLF . self::CHUNK_DATA . Rfc5234::CRLF . 
		')';
	
	/** <samp>chunk-data</samp> */
	public const CHUNK_DATA = '(?:' . Rfc5234::OCTET . '+)';
	
	/** <samp>chunk-ext</samp> */
	public const CHUNK_EXT = '(?:(\;' . self::CHUNK_EXT_NAME . '(?:\=' . self::CHUNK_EXT_VAL . ')?)*)';
	
	/** <samp>chunk-ext-name</samp> */
	public const CHUNK_EXT_NAME = self::TOKEN;
	
	/** <samp>chunk-ext-val</samp> */
	public const CHUNK_EXT_VAL = '(?:' . self::TOKEN . '|' . self::QUOTED_STRING . ')';
	
	/** <samp>chunk-size</samp> */
	public const CHUNK_SIZE = '(?:' . Rfc5234::HEXDIG . '+)';
	
	/** <samp>chunked-body</samp> */
	public const CHUNKED_BODY = '(?:' . self::CHUNK . '*' . self::LAST_CHUNK . self::TRAILER_PART . Rfc5234::CRLF . ')';
	
	/** <samp>comment</samp> */
	public const COMMENT = '(\((?:' . self::CTEXT . '|' . self::QUOTED_PAIR . '|(?-1))*\))';
	
	/** <samp>Connection</samp> */
	public const CONNECTION = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::CONNECTION_OPTION . '(?:' . self::OWS . '\,(?:' . self::OWS . self::CONNECTION_OPTION . ')?)*' . 
		')';
	
	/** <samp>connection-option</samp> */
	public const CONNECTION_OPTION = self::TOKEN;
	
	/** <samp>Content-Length</samp> */
	public const CONTENT_LENGTH = '(?:' . Rfc5234::DIGIT . '+)';
	
	/** <samp>ctext</samp> */
	public const CTEXT = '(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|[\x21-\x27\x2a-\x5b\x5d-\x7e])';
	
	/** <samp>field-content</samp> */
	public const FIELD_CONTENT = '(?:' . 
		self::FIELD_VCHAR . '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')+' . self::FIELD_VCHAR . ')?' . 
		')';
	
	/** <samp>field-name</samp> */
	public const FIELD_NAME = self::TOKEN;
	
	/** <samp>field-value</samp> */
	public const FIELD_VALUE = '(?:' . self::FIELD_CONTENT . '*)';
	
	/** <samp>field-vchar</samp> */
	public const FIELD_VCHAR = Rfc5234::VCHAR;
	
	/** <samp>fragment</samp> */
	public const FRAGMENT = Rfc3986::FRAGMENT;
	
	/** <samp>header-field</samp> */
	public const HEADER_FIELD = '(?:' . self::FIELD_NAME . '\:' . self::OWS . self::FIELD_VALUE . self::OWS . ')';
	
	/** <samp>Host</samp> */
	public const HOST = '(?:' . self::URI_HOST . '(?:\:' . self::PORT . ')?)';
	
	/** <samp>HTTP-message</samp> */
	public const HTTP_MESSAGE = '(?:' . 
		self::START_LINE . 
		'(?:' . self::HEADER_FIELD . Rfc5234::CRLF . ')*' . 
		Rfc5234::CRLF . self::MESSAGE_BODY . '?' . 
		')';
	
	/** <samp>HTTP-name</samp> */
	public const HTTP_NAME = '(?:HTTP)';
	
	/** <samp>http-URI</samp> */
	public const HTTP_URI = '(?:http\:\/\/' . self::AUTHORITY . self::PATH_ABEMPTY . '(?:\?' . self::QUERY . ')?' . 
		'(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>HTTP-version</samp> */
	public const HTTP_VERSION = '(?:' . self::HTTP_NAME . '\/' . Rfc5234::DIGIT . '\.' . Rfc5234::DIGIT . ')';
	
	/** <samp>https-URI</samp> */
	public const HTTPS_URI = '(?:https\:\/\/' . self::AUTHORITY . self::PATH_ABEMPTY . '(?:\?' . self::QUERY . ')?' . 
		'(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>last-chunk</samp> */
	public const LAST_CHUNK = '(?:0+' . self::CHUNK_EXT . '?' . Rfc5234::CRLF . ')';
	
	/** <samp>message-body</samp> */
	public const MESSAGE_BODY = '(?:' . Rfc5234::OCTET . '*)';
	
	/** <samp>method</samp> */
	public const METHOD = self::TOKEN;
	
	/** <samp>origin-form</samp> */
	public const ORIGIN_FORM = '(?:' . self::ABSOLUTE_PATH . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>OWS</samp> */
	public const OWS = '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')*)';
	
	/** <samp>parameter</samp> */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>partial-URI</samp> */
	public const PARTIAL_URI = '(?:' . self::RELATIVE_PART . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>path-abempty</samp> */
	public const PATH_ABEMPTY = Rfc3986::PATH_ABEMPTY;
	
	/** <samp>port</samp> */
	public const PORT = Rfc3986::PORT;
	
	/** <samp>protocol</samp> */
	public const PROTOCOL = '(?:' . self::PROTOCOL_NAME . '(?:\/' . self::PROTOCOL_VERSION . ')?)';
	
	/** <samp>protocol-name</samp> */
	public const PROTOCOL_NAME = self::TOKEN;
	
	/** <samp>protocol-version</samp> */
	public const PROTOCOL_VERSION = self::TOKEN;
	
	/** <samp>pseudonym</samp> */
	public const PSEUDONYM = self::TOKEN;
	
	/** <samp>qdtext</samp> */
	public const QDTEXT = '(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|[\!\x23-\x5b\x5d-\x7e])';
	
	/** <samp>query</samp> */
	public const QUERY = Rfc3986::QUERY;
	
	/** <samp>quoted-pair</samp> */
	public const QUOTED_PAIR = '(?:\\\\(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|' . Rfc5234::VCHAR . '))';
	
	/** <samp>quoted-string</samp> */
	public const QUOTED_STRING = '(?:' . 
		Rfc5234::DQUOTE . '(?:' . self::QDTEXT . '|' . self::QUOTED_PAIR . ')*' . Rfc5234::DQUOTE . 
		')';
	
	/** <samp>rank</samp> */
	public const RANK = '(?:(?:0(?:\.' . Rfc5234::DIGIT . '{0,3})?)|(?:1(?:\.0{0,3})?))';
	
	/** <samp>reason-phrase</samp> */
	public const REASON_PHRASE = '(?:(?:' . Rfc5234::HTAB . '|' . Rfc5234::SP . '|' . Rfc5234::VCHAR . ')*)';
	
	/** <samp>received-by</samp> */
	public const RECEIVED_BY = '(?:' . self::URI_HOST . '(?:\:' . self::PORT . ')?|' . self::PSEUDONYM . ')';
	
	/** <samp>received-protocol</samp> */
	public const RECEIVED_PROTOCOL = '(?:(?:' . self::PROTOCOL_NAME . '\/)?' . self::PROTOCOL_VERSION . ')';
	
	/** <samp>relative-part</samp> */
	public const RELATIVE_PART = Rfc3986::RELATIVE_PART;
	
	/** <samp>request-line</samp> */
	public const REQUEST_LINE = '(?:' . 
		self::METHOD . Rfc5234::SP . self::REQUEST_TARGET . Rfc5234::SP . self::HTTP_VERSION . Rfc5234::CRLF . 
		')';
	
	/** <samp>request-target</samp> */
	public const REQUEST_TARGET = '(?:' . 
		self::ORIGIN_FORM . '|' . self::ABSOLUTE_FORM . '|' . self::AUTHORITY_FORM . '|' . self::ASTERISK_FORM . 
		')';
	
	/** <samp>RWS</samp> */
	public const RWS = '(?:(?:' . Rfc5234::SP . '|' . Rfc5234::HTAB . ')+)';
	
	/** <samp>scheme</samp> */
	public const SCHEME = Rfc3986::SCHEME;
	
	/** <samp>segment</samp> */
	public const SEGMENT = Rfc3986::SEGMENT;
	
	/** <samp>start-line</samp> */
	public const START_LINE = '(?:' . self::REQUEST_LINE . '|' . self::STATUS_LINE . ')';
	
	/** <samp>status-code</samp> */
	public const STATUS_CODE = '(?:' . Rfc5234::DIGIT . '{3})';
	
	/** <samp>status-line</samp> */
	public const STATUS_LINE = '(?:' . 
		self::HTTP_VERSION . Rfc5234::SP . self::STATUS_CODE . Rfc5234::SP . self::REASON_PHRASE . Rfc5234::CRLF . 
		')';
	
	/** <samp>t-codings</samp> */
	public const T_CODINGS = '(?:trailers|' . self::TRANSFER_CODING . self::T_RANKING . '?)';
	
	/** <samp>t-ranking</samp> */
	public const T_RANKING = '(?:' . self::OWS . '\;' . self::OWS . 'q\=' . self::RANK . ')';
	
	/** <samp>tchar</samp> */
	public const TCHAR = '(?:[\!\#\$\%\&\\\'\*\+\-\.\^\_\`\|\~]|' . Rfc5234::DIGIT . '|' . Rfc5234::ALPHA . ')';
	
	/** <samp>TE</samp> */
	public const TE = '(?:' . 
		'(?:(?:\,|' . self::T_CODINGS . ')(?:' . self::OWS . '\,(?:' . self::OWS . self::T_CODINGS . ')?)*)?' . 
		')';
	
	/** <samp>token</samp> */
	public const TOKEN = '(?:' . self::TCHAR . '+)';
	
	/** <samp>Trailer</samp> */
	public const TRAILER = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::FIELD_NAME . '(?:' . self::OWS . '\,(?:' . self::OWS . self::FIELD_NAME . ')?)*' . 
		')';
	
	/** <samp>trailer-part</samp> */
	public const TRAILER_PART = '(?:(?:' . self::HEADER_FIELD . Rfc5234::CRLF . ')*)';
	
	/** <samp>transfer-coding</samp> */
	public const TRANSFER_CODING = '(?:chunked|compress|deflate|gzip|' . self::TRANSFER_EXTENSION . ')';
	
	/** <samp>Transfer-Encoding</samp> */
	public const TRANSFER_ENCODING = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::TRANSFER_CODING . '(?:' . self::OWS . '\,(?:' . self::OWS . self::TRANSFER_CODING . ')?)*' . 
		')';
	
	/** <samp>transfer-extension</samp> */
	public const TRANSFER_EXTENSION = '(?:' . self::TOKEN . '(?:' . self::OWS . '\;' . self::OWS . 
		self::TRANSFER_PARAMETER . ')*)';
	
	/** <samp>transfer-parameter</samp> */
	public const TRANSFER_PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>URI-reference</samp> */
	public const URI_REFERENCE = Rfc3986::URI_REFERENCE;
	
	/** <samp>uri-host</samp> */
	public const URI_HOST = Rfc3986::HOST;
	
	/** <samp>Upgrade</samp> */
	public const UPGRADE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::PROTOCOL . '(?:' . self::OWS . '\,(?:' . self::OWS . self::PROTOCOL . ')?)*' . 
		')';
	
	/** <samp>Via</samp> */
	public const VIA = '(?:' . 
		'(?:\,' . self::OWS . ')*' . 
		self::RECEIVED_PROTOCOL . self::RWS . self::RECEIVED_BY . '(?:' . self::RWS . self::COMMENT . ')?' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::RECEIVED_PROTOCOL . self::RWS . self::RECEIVED_BY . 
			'(?:' . self::RWS . self::COMMENT . ')?)?)*' . 
		')';
}
