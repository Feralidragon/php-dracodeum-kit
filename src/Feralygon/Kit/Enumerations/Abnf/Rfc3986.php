<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Enumerations\Abnf;

use Feralygon\Kit\Enumeration;

/**
 * This enumeration represents RFC 3986 ABNF regular expressions.
 * 
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc3986#appendix-A
 */
class Rfc3986 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> ABNF regular expression. */
	public const ABSOLUTE_URI = '(?:' . self::SCHEME . '\:' . self::HIER_PART . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>authority</samp> ABNF regular expression. */
	public const AUTHORITY = '(?:(?:' . self::USERINFO . '\@)?' . self::HOST . '(?:\:' . self::PORT . ')?)';
	
	/** <samp>dec-octet</samp> ABNF regular expression. */
	public const DEC_OCTET = '(?:\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])';
	
	/** <samp>fragment</samp> ABNF regular expression. */
	public const FRAGMENT = '(?:(?:' . self::PCHAR . '|[\/\?])*)';
	
	/** <samp>gen-delims</samp> ABNF regular expression. */
	public const GEN_DELIMS = '[\:\/\?\#\[\]\@]';
	
	/** <samp>h16</samp> ABNF regular expression. */
	public const H16 = '(?:' . Rfc5234::HEXDIG . '{1,4})';
	
	/** <samp>hier-part</samp> ABNF regular expression. */
	public const HIER_PART = '(?:\/{2}' . self::AUTHORITY . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . 
		self::PATH_ROOTLESS . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>host</samp> ABNF regular expression. */
	public const HOST = '(?:' . self::IP_LITERAL . '|' . self::IPV4_ADDRESS . '|' . self::REG_NAME . ')';
	
	/** <samp>IP-literal</samp> ABNF regular expression. */
	public const IP_LITERAL = '(?:\[(?:' . self::IPV6_ADDRESS . '|' . self::IPVFUTURE . ')\])';
	
	/** <samp>IPv4address</samp> ABNF regular expression. */
	public const IPV4_ADDRESS = '(?:' . self::DEC_OCTET . '(?:\.' . self::DEC_OCTET . '){3})';
	
	/** <samp>IPv6address</samp> ABNF regular expression. */
	public const IPV6_ADDRESS = '(?:' . 
		'(?:' . self::H16 . '\:){6}' . self::LS32 . '|' . 
		'\:\:(?:' . self::H16 . '\:){5}' . self::LS32 . '|' . 
		self::H16 . '?\:\:(?:' . self::H16 . '\:){4}' . self::LS32 . '|' . 
		'(?:(?:' . self::H16 . '\:)?' . self::H16 . ')?\:\:(?:' . self::H16 . '\:){3}' . self::LS32 . '|' . 
		'(?:(?:' . self::H16 . '\:){0,2}' . self::H16 . ')?\:\:(?:' . self::H16 . '\:){2}' . self::LS32 . '|' . 
		'(?:(?:' . self::H16 . '\:){0,3}' . self::H16 . ')?\:\:' . self::H16 . '\:' . self::LS32 . '|' . 
		'(?:(?:' . self::H16 . '\:){0,4}' . self::H16 . ')?\:\:' . self::LS32 . '|' . 
		'(?:(?:' . self::H16 . '\:){0,5}' . self::H16 . ')?\:\:' . self::H16 . '|' . 
		'(?:(?:' . self::H16 . '\:){0,6}' . self::H16 . ')?\:\:' . 
		')';
	
	/** <samp>IPvFuture</samp> ABNF regular expression. */
	public const IPVFUTURE = '(?:v' . Rfc5234::HEXDIG . '+\.' . 
		'(?:' . self::UNRESERVED . '|' . self::SUB_DELIMS . '|\:)+)';
	
	/** <samp>ls32</samp> ABNF regular expression. */
	public const LS32 = '(?:' . self::H16 . ':' . self::H16 . '|' . self::IPV4_ADDRESS . ')';
	
	/** <samp>path</samp> ABNF regular expression. */
	public const PATH = '(?:' . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . self::PATH_NOSCHEME . '|' . 
		self::PATH_ROOTLESS . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>path-abempty</samp> ABNF regular expression. */
	public const PATH_ABEMPTY = '(?:(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>path-absolute</samp> ABNF regular expression. */
	public const PATH_ABSOLUTE = '(?:\/(?:' . self::SEGMENT_NZ . '(?:\/' . self::SEGMENT . ')*)?)';
	
	/** <samp>path-empty</samp> ABNF regular expression. */
	public const PATH_EMPTY = '(?:.{0})';
	
	/** <samp>path-noscheme</samp> ABNF regular expression. */
	public const PATH_NOSCHEME = '(?:' . self::SEGMENT_NZ_NC . '(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>path-rootless</samp> ABNF regular expression. */
	public const PATH_ROOTLESS = '(?:' . self::SEGMENT_NZ . '(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>pchar</samp> ABNF regular expression. */
	public const PCHAR = '(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|[\:\@])';
	
	/** <samp>pct-encoded</samp> ABNF regular expression. */
	public const PCT_ENCODED = '(?:\%' . Rfc5234::HEXDIG . '{2})';
	
	/** <samp>port</samp> ABNF regular expression. */
	public const PORT = '(?:\d*)';
	
	/** <samp>query</samp> ABNF regular expression. */
	public const QUERY = '(?:(?:' . self::PCHAR . '|[\/\?])*)';
	
	/** <samp>reg-name</samp> ABNF regular expression. */
	public const REG_NAME = '(?:(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . ')*)';
	
	/** <samp>relative-part</samp> ABNF regular expression. */
	public const RELATIVE_PART = '(?:\/{2}' . self::AUTHORITY . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . 
		self::PATH_NOSCHEME . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>relative-ref</samp> ABNF regular expression. */
	public const RELATIVE_REF = '(?:' . self::RELATIVE_PART . 
		'(?:\?' . self::QUERY . ')?(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>reserved</samp> ABNF regular expression. */
	public const RESERVED = '(?:' . self::GEN_DELIMS . '|' . self::SUB_DELIMS . ')';
	
	/** <samp>scheme</samp> ABNF regular expression. */
	public const SCHEME = '(?:[A-Za-z][A-Za-z\d\+\-\.]*)';
	
	/** <samp>segment</samp> ABNF regular expression. */
	public const SEGMENT = '(?:' . self::PCHAR . '*)';
	
	/** <samp>segment-nz</samp> ABNF regular expression. */
	public const SEGMENT_NZ = '(?:' . self::PCHAR . '+)';
	
	/** <samp>segment-nz-nc</samp> ABNF regular expression. */
	public const SEGMENT_NZ_NC = '(?:' . 
		'(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|\@)+' . 
		')';
	
	/** <samp>sub-delims</samp> ABNF regular expression. */
	public const SUB_DELIMS = '[\!\$\&\'\(\)\*\+\,\;\=]';
	
	/** <samp>unreserved</samp> ABNF regular expression. */
	public const UNRESERVED = '[A-Za-z\d\-\.\_\~]';
	
	/** <samp>URI</samp> ABNF regular expression. */
	public const URI = '(?:' . self::SCHEME . '\:' . self::HIER_PART . 
		'(?:\?' . self::QUERY . ')?(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>URI-reference</samp> ABNF regular expression. */
	public const URI_REFERENCE = '(?:' . self::URI . '|' . self::RELATIVE_REF . ')';
	
	/** <samp>userinfo</samp> ABNF regular expression. */
	public const USERINFO = '(?:(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|\:)*)';
}
