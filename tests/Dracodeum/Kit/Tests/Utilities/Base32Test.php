<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Base32 as UBase32;
use Dracodeum\Kit\Enumerations\Base32\Alphabet as EAlphabet;
use Dracodeum\Kit\Utilities\Base32\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Base32 */
class Base32Test extends TestCase
{
	//Public methods
	/**
	 * Test <code>encoded</code> method.
	 * 
	 * @dataProvider provideEncodedMethodData
	 * @testdox Base32::encoded('$string', '$alphabet') === $expected
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodedMethod(string $string, string $alphabet, bool $expected): void
	{
		$this->assertSame($expected, UBase32::encoded($string, $alphabet));
	}
	
	/**
	 * Provide <code>encoded</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encoded</code> method data.</p>
	 */
	public function provideEncodedMethodData(): array
	{
		return [
			['', EAlphabet::RFC4648, false],
			[' ', EAlphabet::RFC4648, false],
			['=', EAlphabet::RFC4648, false],
			['a', EAlphabet::RFC4648, false],
			['ME======', EAlphabet::RFC4648, true],
			['ME', EAlphabet::RFC4648, true],
			['ME==', EAlphabet::RFC4648, false],
			['MEI=====', EAlphabet::RFC4648, false],
			['ME======', EAlphabet::ZBASE32, false],
			['cr======', EAlphabet::ZBASE32, true],
			['cr', EAlphabet::ZBASE32, true],
			['HA4A====', EAlphabet::RFC4648, true],
			['HA4A', EAlphabet::RFC4648, true],
			['HA4A==', EAlphabet::RFC4648, false],
			['HA4A======', EAlphabet::RFC4648, false],
			['HA4A', EAlphabet::ZBASE32, false],
			['8yhy====', EAlphabet::ZBASE32, true],
			['8yhy', EAlphabet::ZBASE32, true],
			['MZXW6===', EAlphabet::RFC4648, true],
			['MZXW6', EAlphabet::RFC4648, true],
			['MZXW6====', EAlphabet::RFC4648, false],
			['MZXW6==', EAlphabet::RFC4648, false],
			['MZXW6===', EAlphabet::ZBASE32, false],
			['c3zs6===', EAlphabet::ZBASE32, true],
			['c3zs6', EAlphabet::ZBASE32, true],
			['MJQXEOA=', EAlphabet::RFC4648, true],
			['MJQXEOA', EAlphabet::RFC4648, true],
			['MJQXEOA==', EAlphabet::RFC4648, false],
			['MJQXEOA=', EAlphabet::ZBASE32, false],
			['cjozrqy=', EAlphabet::ZBASE32, true],
			['cjozrqy', EAlphabet::ZBASE32, true],
			['MZXW6YTBOI', EAlphabet::RFC4648, true],
			['MZXW6YTBOI=', EAlphabet::RFC4648, false],
			['MZXW6YTBOI', EAlphabet::ZBASE32, false],
			['c3zs6aubqe', EAlphabet::ZBASE32, true],
			['MZXW6YTBOL6P57QPFYQQ====', EAlphabet::RFC4648, true],
			['MZXW6YTBOL6P57QPFYQQ', EAlphabet::RFC4648, true],
			['MZXW6YTBOL6P57QPFYQQ==', EAlphabet::RFC4648, false],
			['MZXW6YTBOL6P57QPFYQQ====', EAlphabet::ZBASE32, false],
			['c3zs6aubqm6x79oxfaoo====', EAlphabet::ZBASE32, true],
			['c3zs6aubqm6x79oxfaoo', EAlphabet::ZBASE32, true],
			['AUAB6DB77DGS6M36WY======', EAlphabet::RFC4648, true],
			['AUAB6DB77DGS6M36WY', EAlphabet::RFC4648, true],
			['AUAB6DB77DGS6M36WY====', EAlphabet::RFC4648, false],
			['AUAB6DB77DGS6M36WY======', EAlphabet::ZBASE32, false],
			['ywyb6db99dg16c56sa======', EAlphabet::ZBASE32, true],
			['ywyb6db99dg16c56sa', EAlphabet::ZBASE32, true]
		];
	}
	
	/**
	 * Test <code>encode</code> method.
	 * 
	 * @dataProvider provideEncodeMethodData
	 * @testdox Base32::encode('$string', $url_safe, '$alphabet') === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodeMethod(string $string, bool $url_safe, string $alphabet, string $expected): void
	{
		$this->assertSame($expected, UBase32::encode($string, $url_safe, $alphabet));
	}
	
	/**
	 * Provide <code>encode</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encode</code> method data.</p>
	 */
	public function provideEncodeMethodData(): array
	{
		return [
			['', false, EAlphabet::RFC4648, ''],
			['', true, EAlphabet::RFC4648, ''],
			['a', false, EAlphabet::RFC4648, 'ME======'],
			['a', true, EAlphabet::RFC4648, 'ME'],
			['a', true, EAlphabet::ZBASE32, 'cr'],
			['a', true, EAlphabet::GEOHASH, 'd4'],
			['88', false, EAlphabet::RFC4648, 'HA4A===='],
			['88', true, EAlphabet::RFC4648, 'HA4A'],
			['88', true, EAlphabet::ZBASE32, '8yhy'],
			['88', true, EAlphabet::GEOHASH, '70w0'],
			['foo', false, EAlphabet::RFC4648, 'MZXW6==='],
			['foo', true, EAlphabet::RFC4648, 'MZXW6'],
			['foo', true, EAlphabet::ZBASE32, 'c3zs6'],
			['foo', true, EAlphabet::GEOHASH, 'dtrqy'],
			['bar8', false, EAlphabet::RFC4648, 'MJQXEOA='],
			['bar8', true, EAlphabet::RFC4648, 'MJQXEOA'],
			['bar8', true, EAlphabet::ZBASE32, 'cjozrqy'],
			['bar8', true, EAlphabet::GEOHASH, 'd9hr4f0'],
			['foobar', false, EAlphabet::RFC4648, 'MZXW6YTBOI======'],
			['foobar', true, EAlphabet::RFC4648, 'MZXW6YTBOI'],
			['foobar', true, EAlphabet::ZBASE32, 'c3zs6aubqe'],
			['foobar', true, EAlphabet::GEOHASH, 'dtrqysm1f8'],
			['foobarABCD', false, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['foobarABCD', true, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['foobarABCD', true, EAlphabet::ZBASE32, 'c3zs6aubqjywro4r'],
			['foobarABCD', true, EAlphabet::GEOHASH, 'dtrqysm1f90n4hu4'],
			["foobar\xfc\xfe\xfe\x0f", false, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QP'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QP'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::ZBASE32, 'c3zs6aubqm6x79ox'],
			["foobar\xfc\xfe\xfe\x0f", true, EAlphabet::GEOHASH, 'dtrqysm1fcygxzhg'],
			["foobar\xfc\xfe\xfe\x0f.!", false, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QPFYQQ===='],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::RFC4648, 'MZXW6YTBOL6P57QPFYQQ'],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::ZBASE32, 'c3zs6aubqm6x79oxfaoo'],
			["foobar\xfc\xfe\xfe\x0f.!", true, EAlphabet::GEOHASH, 'dtrqysm1fcygxzhg5shh'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", false, EAlphabet::RFC4648, 'RSO2NC2Z5ZJZI6VT'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::RFC4648, 'RSO2NC2Z5ZJZI6VT'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::ZBASE32, 't1q4pn4373j3e6iu'],
			["\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3", true, EAlphabet::GEOHASH, 'jkfue2utxt9t8ypm'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", false, EAlphabet::RFC4648, 'AUAB6DB77DGS6M36WY======'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::RFC4648, 'AUAB6DB77DGS6M36WY'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::ZBASE32, 'ywyb6db99dg16c56sa'],
			["\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6", true, EAlphabet::GEOHASH, '0n01y31zz36kydvyqs'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", false, EAlphabet::RFC4648, '5LGWNIFZSV2R5YMJCNCQ===='],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::RFC4648, '5LGWNIFZSV2R5YMJCNCQ'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::ZBASE32, '7mgspef31i4t7acjnpno'],
			["\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45", true, EAlphabet::GEOHASH, 'xc6qe85tkpujxsd92e2h']
		];
	}
	
	/**
	 * Test <code>decode</code> method.
	 * 
	 * @dataProvider provideDecodeMethodData
	 * @testdox Base32::decode('$string', $alphabet, false|true) === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testDecodeMethod(string $string, string $alphabet, string $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase32::decode($string, $alphabet, $no_throw));
		}
	}
	
	/**
	 * Provide <code>decode</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>decode</code> method data.</p>
	 */
	public function provideDecodeMethodData(): array
	{
		return [
			['ME======', EAlphabet::RFC4648, 'a'],
			['ME', EAlphabet::RFC4648, 'a'],
			['cr', EAlphabet::ZBASE32, 'a'],
			['d4', EAlphabet::GEOHASH, 'a'],
			['HA4A====', EAlphabet::RFC4648, '88'],
			['HA4A', EAlphabet::RFC4648, '88'],
			['8yhy', EAlphabet::ZBASE32, '88'],
			['70w0', EAlphabet::GEOHASH, '88'],
			['MZXW6===', EAlphabet::RFC4648, 'foo'],
			['MZXW6', EAlphabet::RFC4648, 'foo'],
			['c3zs6', EAlphabet::ZBASE32, 'foo'],
			['dtrqy', EAlphabet::GEOHASH, 'foo'],
			['MJQXEOA=', EAlphabet::RFC4648, 'bar8'],
			['MJQXEOA', EAlphabet::RFC4648, 'bar8'],
			['cjozrqy', EAlphabet::ZBASE32, 'bar8'],
			['d9hr4f0', EAlphabet::GEOHASH, 'bar8'],
			['MZXW6YTBOI======', EAlphabet::RFC4648, 'foobar'],
			['MZXW6YTBOI', EAlphabet::RFC4648, 'foobar'],
			['c3zs6aubqe', EAlphabet::ZBASE32, 'foobar'],
			['dtrqysm1f8', EAlphabet::GEOHASH, 'foobar'],
			['MZXW6YTBOJAUEQ2E', EAlphabet::RFC4648, 'foobarABCD'],
			['c3zs6aubqjywro4r', EAlphabet::ZBASE32, 'foobarABCD'],
			['dtrqysm1f90n4hu4', EAlphabet::GEOHASH, 'foobarABCD'],
			['MZXW6YTBOL6P57QP', EAlphabet::RFC4648, "foobar\xfc\xfe\xfe\x0f"],
			['c3zs6aubqm6x79ox', EAlphabet::ZBASE32, "foobar\xfc\xfe\xfe\x0f"],
			['dtrqysm1fcygxzhg', EAlphabet::GEOHASH, "foobar\xfc\xfe\xfe\x0f"],
			['MZXW6YTBOL6P57QPFYQQ====', EAlphabet::RFC4648, "foobar\xfc\xfe\xfe\x0f.!"],
			['MZXW6YTBOL6P57QPFYQQ', EAlphabet::RFC4648, "foobar\xfc\xfe\xfe\x0f.!"],
			['c3zs6aubqm6x79oxfaoo', EAlphabet::ZBASE32, "foobar\xfc\xfe\xfe\x0f.!"],
			['dtrqysm1fcygxzhg5shh', EAlphabet::GEOHASH, "foobar\xfc\xfe\xfe\x0f.!"],
			['RSO2NC2Z5ZJZI6VT', EAlphabet::RFC4648, "\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3"],
			['t1q4pn4373j3e6iu', EAlphabet::ZBASE32, "\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3"],
			['jkfue2utxt9t8ypm', EAlphabet::GEOHASH, "\x8c\x9d\xa6\x8b\x59\xee\x53\x94\x7a\xb3"],
			['AUAB6DB77DGS6M36WY======', EAlphabet::RFC4648, "\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6"],
			['AUAB6DB77DGS6M36WY', EAlphabet::RFC4648, "\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6"],
			['ywyb6db99dg16c56sa', EAlphabet::ZBASE32, "\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6"],
			['0n01y31zz36kydvyqs', EAlphabet::GEOHASH, "\x05\x00\x1f\x0c\x3f\xf8\xcd\x2f\x33\x7e\xb6"],
			['5LGWNIFZSV2R5YMJCNCQ====', EAlphabet::RFC4648, "\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45"],
			['5LGWNIFZSV2R5YMJCNCQ', EAlphabet::RFC4648, "\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45"],
			['7mgspef31i4t7acjnpno', EAlphabet::ZBASE32, "\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45"],
			['xc6qe85tkpujxsd92e2h', EAlphabet::GEOHASH, "\xea\xcd\x66\xa0\xb9\x95\x75\x1e\xe1\x89\x13\x45"]
		];
	}
	
	/**
	 * Test <code>decode</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @dataProvider provideDecodeMethodData_InvalidStringException
	 * @testdox Base32::decode('$string', '$alphabet') --> InvalidString exception
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @return void
	 */
	public function testDecodeMethod_InvalidStringException(string $string, string $alphabet): void
	{
		$this->expectException(Exceptions\Decode\InvalidString::class);
		try {
			UBase32::decode($string, $alphabet);
		} catch (Exceptions\Decode\InvalidString $exception) {
			$this->assertSame($string, $exception->string);
			$this->assertSame($alphabet, $exception->alphabet);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>decode</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @dataProvider provideDecodeMethodData_InvalidStringException
	 * @testdox Base32::decode('$string', '$alphabet', true) === NULL
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet
	 * <p>The method <var>$alphabet</var> parameter to test with.</p>
	 * @return void
	 */
	public function testDecodeMethod_NoThrowNull(string $string, string $alphabet): void
	{
		$this->assertNull(UBase32::decode($string, $alphabet, true));
	}
	
	/**
	 * Provide <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public function provideDecodeMethodData_InvalidStringException(): array
	{
		return [
			['', EAlphabet::RFC4648],
			[' ', EAlphabet::RFC4648],
			['=', EAlphabet::RFC4648],
			['a', EAlphabet::RFC4648],
			['ME==', EAlphabet::RFC4648],
			['MEI=====', EAlphabet::RFC4648],
			['ME======', EAlphabet::ZBASE32],
			['HA4A==', EAlphabet::RFC4648],
			['HA4A======', EAlphabet::RFC4648],
			['HA4A', EAlphabet::ZBASE32],
			['MZXW6====', EAlphabet::RFC4648],
			['MZXW6==', EAlphabet::RFC4648],
			['MZXW6===', EAlphabet::ZBASE32],
			['MJQXEOA==', EAlphabet::RFC4648],
			['MJQXEOA=', EAlphabet::ZBASE32],
			['MZXW6YTBOI=', EAlphabet::RFC4648],
			['MZXW6YTBOI', EAlphabet::ZBASE32],
			['MZXW6YTBOL6P57QPFYQQ==', EAlphabet::RFC4648],
			['MZXW6YTBOL6P57QPFYQQ====', EAlphabet::ZBASE32],
			['AUAB6DB77DGS6M36WY====', EAlphabet::RFC4648],
			['AUAB6DB77DGS6M36WY======', EAlphabet::ZBASE32]
		];
	}
	
	/**
	 * Test <code>normalize</code> method.
	 * 
	 * @dataProvider provideNormalizeMethodData
	 * @testdox Base32::normalize('$string', '$alphabet_from', '$alphabet_to') === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet_from
	 * <p>The method <var>$alphabet_from</var> parameter to test with.</p>
	 * @param string $alphabet_to
	 * <p>The method <var>$alphabet_to</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testNormalizeMethod(
		string $string, string $alphabet_from, string $alphabet_to, string $expected
	): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase32::normalize($string, $alphabet_from, $alphabet_to, $no_throw));
		}
	}
	
	/**
	 * Provide <code>normalize</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>normalize</code> method data.</p>
	 */
	public function provideNormalizeMethodData(): array
	{
		return [
			['ME======', EAlphabet::RFC4648, EAlphabet::RFC4648, 'ME======'],
			['ME', EAlphabet::RFC4648, EAlphabet::RFC4648, 'ME======'],
			['ME', EAlphabet::RFC4648, EAlphabet::ZBASE32, 'cr======'],
			['ME', EAlphabet::RFC4648, EAlphabet::GEOHASH, 'd4======'],
			['cr', EAlphabet::ZBASE32, EAlphabet::RFC4648, 'ME======'],
			['d4', EAlphabet::GEOHASH, EAlphabet::RFC4648, 'ME======'],
			['HA4A====', EAlphabet::RFC4648, EAlphabet::RFC4648, 'HA4A===='],
			['HA4A', EAlphabet::RFC4648, EAlphabet::RFC4648, 'HA4A===='],
			['HA4A', EAlphabet::RFC4648, EAlphabet::ZBASE32, '8yhy===='],
			['HA4A', EAlphabet::RFC4648, EAlphabet::GEOHASH, '70w0===='],
			['8yhy', EAlphabet::ZBASE32, EAlphabet::RFC4648, 'HA4A===='],
			['70w0', EAlphabet::GEOHASH, EAlphabet::RFC4648, 'HA4A===='],
			['MZXW6===', EAlphabet::RFC4648, EAlphabet::RFC4648, 'MZXW6==='],
			['MZXW6', EAlphabet::RFC4648, EAlphabet::RFC4648, 'MZXW6==='],
			['MZXW6', EAlphabet::RFC4648, EAlphabet::ZBASE32, 'c3zs6==='],
			['MZXW6', EAlphabet::RFC4648, EAlphabet::GEOHASH, 'dtrqy==='],
			['c3zs6', EAlphabet::ZBASE32, EAlphabet::RFC4648, 'MZXW6==='],
			['dtrqy', EAlphabet::GEOHASH, EAlphabet::RFC4648, 'MZXW6==='],
			['MJQXEOA=', EAlphabet::RFC4648, EAlphabet::RFC4648, 'MJQXEOA='],
			['MJQXEOA', EAlphabet::RFC4648, EAlphabet::RFC4648, 'MJQXEOA='],
			['MJQXEOA', EAlphabet::RFC4648, EAlphabet::ZBASE32, 'cjozrqy='],
			['MJQXEOA', EAlphabet::RFC4648, EAlphabet::GEOHASH, 'd9hr4f0='],
			['cjozrqy', EAlphabet::ZBASE32, EAlphabet::RFC4648, 'MJQXEOA='],
			['d9hr4f0', EAlphabet::GEOHASH, EAlphabet::RFC4648, 'MJQXEOA='],
			['MZXW6YTBOJAUEQ2E', EAlphabet::RFC4648, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['MZXW6YTBOJAUEQ2E', EAlphabet::RFC4648, EAlphabet::ZBASE32, 'c3zs6aubqjywro4r'],
			['MZXW6YTBOJAUEQ2E', EAlphabet::RFC4648, EAlphabet::GEOHASH, 'dtrqysm1f90n4hu4'],
			['c3zs6aubqjywro4r', EAlphabet::ZBASE32, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E'],
			['dtrqysm1f90n4hu4', EAlphabet::GEOHASH, EAlphabet::RFC4648, 'MZXW6YTBOJAUEQ2E']
		];
	}
	
	/**
	 * Test <code>normalize</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @dataProvider provideNormalizeMethodData_InvalidStringException
	 * @testdox Base32::normalize('$string', '$alphabet_from') --> InvalidString exception
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet_from
	 * <p>The method <var>$alphabet_from</var> parameter to test with.</p>
	 * @return void
	 */
	public function testNormalizeMethod_InvalidStringException(string $string, string $alphabet_from): void
	{
		$this->expectException(Exceptions\Normalize\InvalidString::class);
		try {
			UBase32::normalize($string, $alphabet_from);
		} catch (Exceptions\Normalize\InvalidString $exception) {
			$this->assertSame($string, $exception->string);
			$this->assertSame($alphabet_from, $exception->alphabet);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>normalize</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @dataProvider provideNormalizeMethodData_InvalidStringException
	 * @testdox Base32::normalize('$string', '$alphabet_from', EAlphabet::RFC4648, true) === NULL
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $alphabet_from
	 * <p>The method <var>$alphabet_from</var> parameter to test with.</p>
	 * @return void
	 */
	public function testNormalizeMethod_NoThrowNull(string $string, string $alphabet_from): void
	{
		$this->assertNull(UBase32::normalize($string, $alphabet_from, EAlphabet::RFC4648, true));
	}
	
	/**
	 * Provide <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public function provideNormalizeMethodData_InvalidStringException(): array
	{
		return [
			['', EAlphabet::RFC4648],
			[' ', EAlphabet::RFC4648],
			['=', EAlphabet::RFC4648],
			['a', EAlphabet::RFC4648],
			['ME==', EAlphabet::RFC4648],
			['MEI=====', EAlphabet::RFC4648],
			['ME======', EAlphabet::ZBASE32],
			['HA4A==', EAlphabet::RFC4648],
			['HA4A======', EAlphabet::RFC4648],
			['HA4A', EAlphabet::ZBASE32],
			['MZXW6====', EAlphabet::RFC4648],
			['MZXW6==', EAlphabet::RFC4648],
			['MZXW6===', EAlphabet::ZBASE32],
			['MJQXEOA==', EAlphabet::RFC4648],
			['MJQXEOA=', EAlphabet::ZBASE32],
			['MZXW6YTBOI=', EAlphabet::RFC4648],
			['MZXW6YTBOI', EAlphabet::ZBASE32],
			['MZXW6YTBOL6P57QPFYQQ==', EAlphabet::RFC4648],
			['MZXW6YTBOL6P57QPFYQQ====', EAlphabet::ZBASE32],
			['AUAB6DB77DGS6M36WY====', EAlphabet::RFC4648],
			['AUAB6DB77DGS6M36WY======', EAlphabet::ZBASE32]
		];
	}
}
