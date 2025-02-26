<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations\Abnf;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents RFC 3986 ABNF regular expressions.
 * 
 * @see https://tools.ietf.org/html/rfc3986#appendix-A
 */
class Rfc3986 extends Enumeration
{
	//Public constants
	/** <samp>absolute-URI</samp> */
	public const ABSOLUTE_URI = '(?:' . self::SCHEME . '\:' . self::HIER_PART . '(?:\?' . self::QUERY . ')?)';
	
	/** <samp>authority</samp> */
	public const AUTHORITY = '(?:(?:' . self::USERINFO . '\@)?' . self::HOST . '(?:\:' . self::PORT . ')?)';
	
	/** <samp>dec-octet</samp> */
	public const DEC_OCTET = '(?:' . 
		Rfc5234::DIGIT . '|[1-9]' . Rfc5234::DIGIT . '|1' . Rfc5234::DIGIT . '{2}|' . 
		'2[0-4]' . Rfc5234::DIGIT . '|25[0-5]' . 
		')';
	
	/** <samp>fragment</samp> */
	public const FRAGMENT = '(?:(?:' . self::PCHAR . '|[\/\?])*)';
	
	/** <samp>gen-delims</samp> */
	public const GEN_DELIMS = '[\:\/\?\#\[\]\@]';
	
	/** <samp>h16</samp> */
	public const H16 = '(?:' . Rfc5234::HEXDIG . '{1,4})';
	
	/** <samp>hier-part</samp> */
	public const HIER_PART = '(?:\/{2}' . self::AUTHORITY . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . 
		self::PATH_ROOTLESS . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>host</samp> */
	public const HOST = '(?:' . self::IP_LITERAL . '|' . self::IPV4_ADDRESS . '|' . self::REG_NAME . ')';
	
	/** <samp>IP-literal</samp> */
	public const IP_LITERAL = '(?:\[(?:' . self::IPV6_ADDRESS . '|' . self::IPVFUTURE . ')\])';
	
	/** <samp>IPv4address</samp> */
	public const IPV4_ADDRESS = '(?:' . self::DEC_OCTET . '(?:\.' . self::DEC_OCTET . '){3})';
	
	/** <samp>IPv6address</samp> */
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
	
	/** <samp>IPvFuture</samp> */
	public const IPVFUTURE = '(?:v' . Rfc5234::HEXDIG . '+\.' . 
		'(?:' . self::UNRESERVED . '|' . self::SUB_DELIMS . '|\:)+)';
	
	/** <samp>ls32</samp> */
	public const LS32 = '(?:' . self::H16 . '\:' . self::H16 . '|' . self::IPV4_ADDRESS . ')';
	
	/** <samp>path</samp> */
	public const PATH = '(?:' . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . self::PATH_NOSCHEME . '|' . 
		self::PATH_ROOTLESS . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>path-abempty</samp> */
	public const PATH_ABEMPTY = '(?:(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>path-absolute</samp> */
	public const PATH_ABSOLUTE = '(?:\/(?:' . self::SEGMENT_NZ . '(?:\/' . self::SEGMENT . ')*)?)';
	
	/** <samp>path-empty</samp> */
	public const PATH_EMPTY = '(?:)';
	
	/** <samp>path-noscheme</samp> */
	public const PATH_NOSCHEME = '(?:' . self::SEGMENT_NZ_NC . '(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>path-rootless</samp> */
	public const PATH_ROOTLESS = '(?:' . self::SEGMENT_NZ . '(?:\/' . self::SEGMENT . ')*)';
	
	/** <samp>pchar</samp> */
	public const PCHAR = '(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|[\:\@])';
	
	/** <samp>pct-encoded</samp> */
	public const PCT_ENCODED = '(?:\%' . Rfc5234::HEXDIG . '{2})';
	
	/** <samp>port</samp> */
	public const PORT = '(?:' . Rfc5234::DIGIT . '*)';
	
	/** <samp>query</samp> */
	public const QUERY = '(?:(?:' . self::PCHAR . '|[\/\?])*)';
	
	/** <samp>reg-name</samp> */
	public const REG_NAME = '(?:(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . ')*)';
	
	/** <samp>relative-part</samp> */
	public const RELATIVE_PART = '(?:\/{2}' . self::AUTHORITY . self::PATH_ABEMPTY . '|' . self::PATH_ABSOLUTE . '|' . 
		self::PATH_NOSCHEME . '|' . self::PATH_EMPTY . ')';
	
	/** <samp>relative-ref</samp> */
	public const RELATIVE_REF = '(?:' . self::RELATIVE_PART . 
		'(?:\?' . self::QUERY . ')?(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>reserved</samp> */
	public const RESERVED = '(?:' . self::GEN_DELIMS . '|' . self::SUB_DELIMS . ')';
	
	/** <samp>scheme</samp> */
	public const SCHEME = '(?:' . Rfc5234::ALPHA . '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\+\-\.])*)';
	
	/** <samp>segment</samp> */
	public const SEGMENT = '(?:' . self::PCHAR . '*)';
	
	/** <samp>segment-nz</samp> */
	public const SEGMENT_NZ = '(?:' . self::PCHAR . '+)';
	
	/** <samp>segment-nz-nc</samp> */
	public const SEGMENT_NZ_NC = '(?:' . 
		'(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|\@)+' . 
		')';
	
	/** <samp>sub-delims</samp> */
	public const SUB_DELIMS = '[\!\$\&\'\(\)\*\+\,\;\=]';
	
	/** <samp>unreserved</samp> */
	public const UNRESERVED = '(?:' . Rfc5234::ALPHA . '|' . Rfc5234::DIGIT . '|[\-\.\_\~])';
	
	/** <samp>URI</samp> */
	public const URI = '(?:' . self::SCHEME . '\:' . self::HIER_PART . 
		'(?:\?' . self::QUERY . ')?(?:\#' . self::FRAGMENT . ')?)';
	
	/** <samp>URI-reference</samp> */
	public const URI_REFERENCE = '(?:' . self::URI . '|' . self::RELATIVE_REF . ')';
	
	/** <samp>userinfo</samp> */
	public const USERINFO = '(?:(?:' . self::UNRESERVED . '|' . self::PCT_ENCODED . '|' . self::SUB_DELIMS . '|\:)*)';
}
