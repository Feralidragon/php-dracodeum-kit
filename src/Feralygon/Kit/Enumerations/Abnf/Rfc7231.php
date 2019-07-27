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
 * @see https://tools.ietf.org/html/rfc7231#appendix-C
 * @see https://tools.ietf.org/html/rfc7231#appendix-D
 */
class Rfc7231 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> ABNF regular expression. */
	public const ABSOLUTE_URI = Rfc7230::ABSOLUTE_URI;
	
	/** <samp>Accept</samp> ABNF regular expression. */
	public const ACCEPT = '(?:(?:' . 
		'(?:\,|' . self::MEDIA_RANGE . self::ACCEPT_PARAMS . '?)' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::MEDIA_RANGE . self::ACCEPT_PARAMS . '?)?)*' . 
		')?)';
	
	/** <samp>Accept-Charset</samp> ABNF regular expression. */
	public const ACCEPT_CHARSET = '(?:' . 
		'(?:\,' . self::OWS . ')*(?:' . self::CHARSET . '|\*)' . self::WEIGHT . '?' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . '(?:' . self::CHARSET . '|\*)' . self::WEIGHT . '?)?)*' . 
		')';
	
	/** <samp>Accept-Encoding</samp> ABNF regular expression. */
	public const ACCEPT_ENCODING = '(?:(?:' . 
		'(?:\,|' . self::CODINGS . self::WEIGHT . '?)' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CODINGS . self::WEIGHT . '?)?)*' . 
		')?)';
	
	/** <samp>accept-ext</samp> ABNF regular expression. */
	public const ACCEPT_EXT = '(?:' . 
		self::OWS . '\;' . self::OWS . self::TOKEN . '(?:\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))?' . 
		')';
	
	/** <samp>Accept-Language</samp> ABNF regular expression. */
	public const ACCEPT_LANGUAGE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::LANGUAGE_RANGE . self::WEIGHT . '?' . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::LANGUAGE_RANGE . self::WEIGHT . '?)?)*' . 
		')';
	
	/** <samp>accept-params</samp> ABNF regular expression. */
	public const ACCEPT_PARAMS = '(?:' . self::WEIGHT . self::ACCEPT_EXT . '*)';
	
	/** <samp>Allow</samp> ABNF regular expression. */
	public const ALLOW = '(?:(?:' . 
		'(?:\,|' . self::METHOD . ')(?:' . self::OWS . '\,(?:' . self::OWS . self::METHOD . ')?)*' . 
		')?)';
	
	/** <samp>asctime-date</samp> ABNF regular expression. */
	public const ASCTIME_DATE = '(?:' . 
		self::DAY_NAME . Rfc5234::SP . self::DATE3 . Rfc5234::SP . self::TIME_OF_DAY . Rfc5234::SP . self::YEAR . 
		')';
	
	/** <samp>charset</samp> ABNF regular expression. */
	public const CHARSET = self::TOKEN;
	
	/** <samp>codings</samp> ABNF regular expression. */
	public const CODINGS = '(?:' . self::CONTENT_CODING . '|identity|\*)';
	
	/** <samp>comment</samp> ABNF regular expression. */
	public const COMMENT = Rfc7230::COMMENT;
	
	/** <samp>content-coding</samp> ABNF regular expression. */
	public const CONTENT_CODING = self::TOKEN;
	
	/** <samp>Content-Encoding</samp> ABNF regular expression. */
	public const CONTENT_ENCODING = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::CONTENT_CODING . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::CONTENT_CODING . ')?)*' . 
		')';
	
	/** <samp>Content-Language</samp> ABNF regular expression. */
	public const CONTENT_LANGUAGE = '(?:' . 
		'(?:\,' . self::OWS . ')*' . self::LANGUAGE_TAG . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::LANGUAGE_TAG . ')?)*' . 
		')';
	
	/** <samp>Content-Location</samp> ABNF regular expression. */
	public const CONTENT_LOCATION = '(?:' . self::ABSOLUTE_URI . '|' . self::PARTIAL_URI . ')';
	
	/** <samp>Content-Type</samp> ABNF regular expression. */
	public const CONTENT_TYPE = self::MEDIA_TYPE;
	
	/** <samp>Date</samp> ABNF regular expression. */
	public const DATE = self::HTTP_DATE;
	
	/** <samp>date1</samp> ABNF regular expression. */
	public const DATE1 = '(?:' . self::DAY . Rfc5234::SP . self::MONTH . Rfc5234::SP . self::YEAR . ')';
	
	/** <samp>date2</samp> ABNF regular expression. */
	public const DATE2 = '(?:' . self::DAY . '\-' . self::MONTH . '\-' . Rfc5234::DIGIT . '{2})';
	
	/** <samp>date3</samp> ABNF regular expression. */
	public const DATE3 = '(?:' . 
		self::MONTH . Rfc5234::SP . '(?:' . Rfc5234::DIGIT . '{2}|' . Rfc5234::SP . Rfc5234::DIGIT . ')' . 
		')';
	
	/** <samp>day</samp> ABNF regular expression. */
	public const DAY = '(?:' . Rfc5234::DIGIT . '{2})';
	
	/** <samp>day-name</samp> ABNF regular expression. */
	public const DAY_NAME = '(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun)';
	
	/** <samp>day-name-l</samp> ABNF regular expression. */
	public const DAY_NAME_L = '(?:Monday|Tuesday|Wednesday|Thursday|Friday|Saturday|Sunday)';
	
	/** <samp>delay-seconds</samp> ABNF regular expression. */
	public const DELAY_SECONDS = '(?:' . Rfc5234::DIGIT . '+)';
	
	/** <samp>Expect</samp> ABNF regular expression. */
	public const EXPECT = '(?:100\-continue)';
	
	/** <samp>field-name</samp> ABNF regular expression. */
	public const FIELD_NAME = Rfc7230::FIELD_NAME;
	
	/** <samp>From</samp> ABNF regular expression. */
	//public const FROM = mailbox //TODO (depends on RFC 5322)
	
	/** <samp>GMT</samp> ABNF regular expression. */
	public const GMT = '(?:GMT)';
	
	/** <samp>hour</samp> ABNF regular expression. */
	public const HOUR = '(?:' . Rfc5234::DIGIT . '{2})';
	
	/** <samp>HTTP-date</samp> ABNF regular expression. */
	public const HTTP_DATE = '(?:' . self::IMF_FIXDATE . '|' . self::OBS_DATE . ')';
	
	/** <samp>IMF-fixdate</samp> ABNF regular expression. */
	public const IMF_FIXDATE = '(?:' . 
		self::DAY_NAME . '\,' . Rfc5234::SP . self::DATE1 . Rfc5234::SP . self::TIME_OF_DAY . Rfc5234::SP . self::GMT . 
		')';
	
	/** <samp>language-range</samp> ABNF regular expression. */
	public const LANGUAGE_RANGE = Rfc4647::LANGUAGE_RANGE;
	
	/** <samp>language-tag</samp> ABNF regular expression. */
	public const LANGUAGE_TAG = Rfc5646::LANGUAGE_TAG;
	
	/** <samp>Location</samp> ABNF regular expression. */
	public const LOCATION = self::URI_REFERENCE;
	
	/** <samp>mailbox</samp> ABNF regular expression. */
	//public const MAILBOX = <mailbox, see [RFC5322], Section 3.4> //TODO (depends on RFC 5322)
	
	/** <samp>Max-Forwards</samp> ABNF regular expression. */
	public const MAX_FORWARDS = '(?:' . Rfc5234::DIGIT . '+)';
	
	/** <samp>media-range</samp> ABNF regular expression. */
	public const MEDIA_RANGE = '(?:' . 
		'(?:\*\/\*|' . self::TYPE . '\/\*|' . self::TYPE . '\/' . self::SUBTYPE . ')' . 
		'(?:' . self::OWS . '\;' . self::OWS . self::PARAMETER . ')*' . 
		')';
	
	/** <samp>media-type</samp> ABNF regular expression. */
	public const MEDIA_TYPE = '(?:' . 
		self::TYPE . '\/' . self::SUBTYPE . '(?:' . self::OWS . '\;' . self::OWS . self::PARAMETER . ')*' . 
		')';
	
	/** <samp>method</samp> ABNF regular expression. */
	public const METHOD = self::TOKEN;
	
	/** <samp>minute</samp> ABNF regular expression. */
	public const MINUTE = '(?:' . Rfc5234::DIGIT . '{2})';
	
	/** <samp>month</samp> ABNF regular expression. */
	public const MONTH = '(?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)';
	
	/** <samp>obs-date</samp> ABNF regular expression. */
	public const OBS_DATE = '(?:' . self::RFC850_DATE . '|' . self::ASCTIME_DATE . ')';
	
	/** <samp>OWS</samp> ABNF regular expression. */
	public const OWS = Rfc7230::OWS;
	
	/** <samp>parameter</samp> ABNF regular expression. */
	public const PARAMETER = '(?:' . self::TOKEN . '\=(?:' . self::TOKEN . '|' . self::QUOTED_STRING . '))';
	
	/** <samp>partial-URI</samp> ABNF regular expression. */
	public const PARTIAL_URI = Rfc7230::PARTIAL_URI;
	
	/** <samp>product</samp> ABNF regular expression. */
	public const PRODUCT = '(?:' . self::TOKEN . '(?:\/' . self::PRODUCT_VERSION . ')?)';
	
	/** <samp>product-version</samp> ABNF regular expression. */
	public const PRODUCT_VERSION = self::TOKEN;
	
	/** <samp>quoted-string</samp> ABNF regular expression. */
	public const QUOTED_STRING = Rfc7230::QUOTED_STRING;
	
	/** <samp>qvalue</samp> ABNF regular expression. */
	public const QVALUE = '(?:0(?:\.' . Rfc5234::DIGIT . '{0,3})?|1(?:\.0{0,3})?)';
	
	/** <samp>Referer</samp> ABNF regular expression. */
	public const REFERER = '(?:' . self::ABSOLUTE_URI . '|' . self::PARTIAL_URI . ')';
	
	/** <samp>Retry-After</samp> ABNF regular expression. */
	public const RETRY_AFTER = '(?:' . self::HTTP_DATE . '|' . self::DELAY_SECONDS . ')';
	
	/** <samp>rfc850-date</samp> ABNF regular expression. */
	public const RFC850_DATE = '(?:' . 
		self::DAY_NAME_L . '\,' . Rfc5234::SP . self::DATE2 . Rfc5234::SP . self::TIME_OF_DAY . Rfc5234::SP . 
			self::GMT . 
		')';
	
	/** <samp>RWS</samp> ABNF regular expression. */
	public const RWS = Rfc7230::RWS;
	
	/** <samp>second</samp> ABNF regular expression. */
	public const SECOND = '(?:' . Rfc5234::DIGIT . '{2})';
	
	/** <samp>Server</samp> ABNF regular expression. */
	public const SERVER = '(?:' . 
		self::PRODUCT . '(?:' . self::RWS . '(?:' . self::PRODUCT . '|' . self::COMMENT . '))*' . 
		')';
	
	/** <samp>subtype</samp> ABNF regular expression. */
	public const SUBTYPE = self::TOKEN;
	
	/** <samp>time-of-day</samp> ABNF regular expression. */
	public const TIME_OF_DAY = '(?:' . self::HOUR . '\:' . self::MINUTE . '\:' . self::SECOND . ')';
	
	/** <samp>token</samp> ABNF regular expression. */
	public const TOKEN = Rfc7230::TOKEN;
	
	/** <samp>type</samp> ABNF regular expression. */
	public const TYPE = self::TOKEN;
	
	/** <samp>URI-reference</samp> ABNF regular expression. */
	public const URI_REFERENCE = Rfc7230::URI_REFERENCE;
	
	/** <samp>User-Agent</samp> ABNF regular expression. */
	public const USER_AGENT = '(?:' . 
		self::PRODUCT . '(?:' . self::RWS . '(?:' . self::PRODUCT . '|' . self::COMMENT . '))*' . 
		')';
	
	/** <samp>Vary</samp> ABNF regular expression. */
	public const VARY = '(?:\*|' . 
		'(?:\,' . self::OWS . ')*' . self::FIELD_NAME . 
		'(?:' . self::OWS . '\,(?:' . self::OWS . self::FIELD_NAME . ')?)*' . 
		')';
	
	/** <samp>weight</samp> ABNF regular expression. */
	public const WEIGHT = '(?:' . self::OWS . '\;' . self::OWS . '[Qq]\=' . self::QVALUE . ')';
	
	/** <samp>year</samp> ABNF regular expression. */
	public const YEAR = '(?:' . Rfc5234::DIGIT . '{4})';
}
