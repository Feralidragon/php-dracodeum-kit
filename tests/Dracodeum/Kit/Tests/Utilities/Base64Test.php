<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Base64 as UBase64;
use Dracodeum\Kit\Utilities\Base64\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Base64 */
class Base64Test extends TestCase
{
	//Public methods
	/**
	 * Test <code>encoded</code> method.
	 * 
	 * @testdox Base64::encoded('$string', $url_safe) === $expected
	 * @dataProvider provideEncodedData
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 */
	public function testEncoded(string $string, ?bool $url_safe, bool $expected): void
	{
		$this->assertSame($expected, UBase64::encoded($string, $url_safe));
	}
	
	/**
	 * Test <code>encode</code> method.
	 * 
	 * @testdox Base64::encode('$string', $url_safe) === '$expected'
	 * @dataProvider provideEncodeData
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 */
	public function testEncode(string $string, bool $url_safe, string $expected): void
	{
		$this->assertSame($expected, UBase64::encode($string, $url_safe));
	}
	
	/**
	 * Test <code>decode</code> method.
	 * 
	 * @testdox Base64::decode('$string', $url_safe, false|true) === '$expected'
	 * @dataProvider provideDecodeData
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 */
	public function testDecode(string $string, ?bool $url_safe, string $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase64::decode($string, $url_safe, $no_throw));
		}
	}
	
	/**
	 * Test <code>decode</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @testdox Base64::decode('$string', $url_safe) --> InvalidString exception
	 * @dataProvider provideDecodeData_Exception_InvalidString
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 */
	public function testDecode_Exception_InvalidString(string $string, ?bool $url_safe): void
	{
		$this->expectException(Exceptions\Decode\InvalidString::class);
		try {
			UBase64::decode($string, $url_safe);
		} catch (Exceptions\Decode\InvalidString $exception) {
			$this->assertSame($string, $exception->string);
			$this->assertSame($url_safe ?? false, $exception->url_safe);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>decode</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @testdox Base64::decode('$string', $url_safe, true) === null
	 * @dataProvider provideDecodeData_Exception_InvalidString
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 */
	public function testDecode_NoThrow_Null(string $string, ?bool $url_safe): void
	{
		$this->assertNull(UBase64::decode($string, $url_safe, true));
	}
	
	/**
	 * Test <code>normalize</code> method.
	 * 
	 * @testdox Base64::normalize('$string', false|true) === '$expected'
	 * @dataProvider provideNormalizeData
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 */
	public function testNormalize(string $string, string $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase64::normalize($string, $no_throw));
		}
	}
	
	/**
	 * Test <code>normalize</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @testdox Base64::normalize('$string') --> InvalidString exception
	 * @dataProvider provideNormalizeData_Exception_InvalidString
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 */
	public function testNormalize_Exception_InvalidString(string $string): void
	{
		$this->expectException(Exceptions\Normalize\InvalidString::class);
		try {
			UBase64::normalize($string);
		} catch (Exceptions\Normalize\InvalidString $exception) {
			$this->assertSame($string, $exception->string);
			throw $exception;
		}
	}
	
	/**
	 * Test <code>normalize</code> method with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @testdox Base64::normalize('$string', true) === null
	 * @dataProvider provideNormalizeData_Exception_InvalidString
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 */
	public function testNormalize_NoThrow_Null(string $string): void
	{
		$this->assertNull(UBase64::normalize($string, true));
	}
	
	
	
	//Public static methods
	/**
	 * Provide <code>encoded</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encoded</code> method data.</p>
	 */
	public static function provideEncodedData(): array
	{
		return [
			['', null, false],
			['', false, false],
			['', true, false],
			[' ', null, false],
			[' ', false, false],
			[' ', true, false],
			['=', null, false],
			['=', false, false],
			['=', true, false],
			['a', null, false],
			['a', false, false],
			['a', true, false],
			['$a#b', null, false],
			['$a#b', false, false],
			['$a#b', true, false],
			['aBCd', null, true],
			['aBCd', false, true],
			['aBCd', true, true],
			['aB/C', null, true],
			['aB/C=', null, false],
			['aB/C==', null, false],
			['aB/Cd', null, false],
			['aB/Cd=', null, false],
			['aB/Cd==', null, false],
			['aB/Cd3', null, true],
			['aB/Cd3=', null, false],
			['aB/Cd3==', null, true],
			['aB/Cd3+', null, true],
			['aB/Cd3+=', null, true],
			['aB/Cd3+==', null, false],
			['aB/Cd3+', false, true],
			['aB/Cd3+=', false, true],
			['aB/Cd3+==', false, false],
			['aB/Cd3+', true, false],
			['aB/Cd3+=', true, false],
			['aB/Cd3+==', true, false],
			['aB_Cd3-', null, true],
			['aB_Cd3-=', null, false],
			['aB_Cd3-==', null, false],
			['aB_Cd3-', false, false],
			['aB_Cd3-=', false, false],
			['aB_Cd3-==', false, false],
			['aB_Cd3-', true, true],
			['aB_Cd3-=', true, false],
			['aB_Cd3-==', true, false],
			['aB/Cd3-', null, false],
			['aB_Cd3+=', null, false],
			['aB/Cd3-', false, false],
			['aB_Cd3+=', false, false],
			['aB/Cd3-', true, false],
			['aB_Cd3+=', true, false]
		];
	}
	
	/**
	 * Provide <code>encode</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>encode</code> method data.</p>
	 */
	public static function provideEncodeData(): array
	{
		return [
			['', false, ''],
			['', true, ''],
			['a', false, 'YQ=='],
			['a', true, 'YQ'],
			['foo', false, 'Zm9v'],
			['foo', true, 'Zm9v'],
			['foobar', false, 'Zm9vYmFy'],
			['foobar', true, 'Zm9vYmFy'],
			['foobarABCD', false, 'Zm9vYmFyQUJDRA=='],
			['foobarABCD', true, 'Zm9vYmFyQUJDRA'],
			["foobar\xfc\xfe\xfe\x0f", false, 'Zm9vYmFy/P7+Dw=='],
			["foobar\xfc\xfe\xfe\x0f", true, 'Zm9vYmFy_P7-Dw'],
			["foobar\xfc\xfe\xfe\x0f.!", false, 'Zm9vYmFy/P7+Dy4h'],
			["foobar\xfc\xfe\xfe\x0f.!", true, 'Zm9vYmFy_P7-Dy4h']
		];
	}
	
	/**
	 * Provide <code>decode</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>decode</code> method data.</p>
	 */
	public static function provideDecodeData(): array
	{
		return [
			['YQ==', null, 'a'],
			['YQ', null, 'a'],
			['YQ==', false, 'a'],
			['YQ', true, 'a'],
			['Zm9v', null, 'foo'],
			['Zm9v', false, 'foo'],
			['Zm9v', true, 'foo'],
			['Zm9vYmFy', null, 'foobar'],
			['Zm9vYmFy', false, 'foobar'],
			['Zm9vYmFy', true, 'foobar'],
			['Zm9vYmFyQUJDRA==', null, 'foobarABCD'],
			['Zm9vYmFyQUJDRA', null, 'foobarABCD'],
			['Zm9vYmFyQUJDRA==', false, 'foobarABCD'],
			['Zm9vYmFyQUJDRA', false, 'foobarABCD'],
			['Zm9vYmFyQUJDRA', true, 'foobarABCD'],
			['Zm9vYmFy/P7+Dw==', null, "foobar\xfc\xfe\xfe\x0f"],
			['Zm9vYmFy/P7+Dw', null, "foobar\xfc\xfe\xfe\x0f"],
			['Zm9vYmFy/P7+Dw==', false, "foobar\xfc\xfe\xfe\x0f"],
			['Zm9vYmFy/P7+Dw', false, "foobar\xfc\xfe\xfe\x0f"],
			['Zm9vYmFy_P7-Dw', true, "foobar\xfc\xfe\xfe\x0f"],
			['Zm9vYmFy/P7+Dy4h', null, "foobar\xfc\xfe\xfe\x0f.!"],
			['Zm9vYmFy/P7+Dy4h', false, "foobar\xfc\xfe\xfe\x0f.!"],
			['Zm9vYmFy_P7-Dy4h', true, "foobar\xfc\xfe\xfe\x0f.!"]
		];
	}
	
	/**
	 * Provide <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public static function provideDecodeData_Exception_InvalidString(): array
	{
		return [
			['', null],
			['', false],
			['', true],
			[' ', null],
			[' ', false],
			[' ', true],
			['=', null],
			['=', false],
			['=', true],
			['a', null],
			['a', false],
			['a', true],
			['YQ=', null],
			['YQ=', false],
			['YQ=', true],
			['YQ==', true],
			['$a#b', null],
			['$a#b', false],
			['$a#b', true],
			['Zm9vYmFyQUJDRA=', null],
			['Zm9vYmFyQUJDRA=', false],
			['Zm9vYmFyQUJDRA=', true],
			['Zm9vYmFyQUJDRA==', true],
			['Zm9vYmFy/P7+Dw==', true],
			['Zm9vYmFy_P7-Dw==', true],
			['Zm9vYmFy_P7-Dw', false],
			['Zm9vYmFy/P7+Dy4h', true],
			['Zm9vYmFy_P7-Dy4h', false]
		];
	}
	
	/**
	 * Provide <code>normalize</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>normalize</code> method data.</p>
	 */
	public static function provideNormalizeData(): array
	{
		return [
			['YQ==', 'YQ=='],
			['YQ', 'YQ=='],
			['Zm9v', 'Zm9v'],
			['Zm9vYmFy', 'Zm9vYmFy'],
			['Zm9vYmFyQUJDRA==', 'Zm9vYmFyQUJDRA=='],
			['Zm9vYmFyQUJDRA', 'Zm9vYmFyQUJDRA=='],
			['Zm9vYmFy/P7+Dw==', 'Zm9vYmFy/P7+Dw=='],
			['Zm9vYmFy/P7+Dw', 'Zm9vYmFy/P7+Dw=='],
			['Zm9vYmFy_P7-Dw', 'Zm9vYmFy/P7+Dw=='],
			['Zm9vYmFy/P7+Dy4h', 'Zm9vYmFy/P7+Dy4h'],
			['Zm9vYmFy_P7-Dy4h', 'Zm9vYmFy/P7+Dy4h']
		];
	}
	
	/**
	 * Provide <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public static function provideNormalizeData_Exception_InvalidString(): array
	{
		return [
			[''],
			[' '],
			['='],
			['a'],
			['YQ='],
			['$a#b'],
			['Zm9vYmFyQUJDRA='],
			['Zm9vYmFy/P7+Dw='],
			['Zm9vYmFy_P7-Dw==']
		];
	}
}
