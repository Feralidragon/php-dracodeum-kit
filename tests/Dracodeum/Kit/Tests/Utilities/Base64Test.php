<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	 * @dataProvider provideEncodedMethodData
	 * @testdox Base64::encoded('$string', $url_safe) === $expected
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodedMethod(string $string, ?bool $url_safe, bool $expected): void
	{
		$this->assertSame($expected, UBase64::encoded($string, $url_safe));
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
	 * Test <code>encode</code> method.
	 * 
	 * @dataProvider provideEncodeMethodData
	 * @testdox Base64::encode('$string', $url_safe) === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testEncodeMethod(string $string, bool $url_safe, string $expected): void
	{
		$this->assertSame($expected, UBase64::encode($string, $url_safe));
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
	 * Test <code>decode</code> method.
	 * 
	 * @dataProvider provideDecodeMethodData
	 * @testdox Base64::decode('$string', $url_safe, false|true) === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testDecodeMethod(string $string, ?bool $url_safe, string $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase64::decode($string, $url_safe, $no_throw));
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
	 * Test <code>decode</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @dataProvider provideDecodeMethodDataForInvalidStringException
	 * @testdox Base64::decode('$string', $url_safe) --> InvalidString exception
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @return void
	 */
	public function testDecodeMethodInvalidStringException(string $string, ?bool $url_safe): void
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
	 * @dataProvider provideDecodeMethodDataForInvalidStringException
	 * @testdox Base64::decode('$string', $url_safe, true) === NULL
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param bool|null $url_safe
	 * <p>The method <var>$url_safe</var> parameter to test with.</p>
	 * @return void
	 */
	public function testDecodeMethodNoThrowNull(string $string, ?bool $url_safe): void
	{
		$this->assertNull(UBase64::decode($string, $url_safe, true));
	}
	
	/**
	 * Provide <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>decode</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public function provideDecodeMethodDataForInvalidStringException(): array
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
	 * Test <code>normalize</code> method.
	 * 
	 * @dataProvider provideNormalizeMethodData
	 * @testdox Base64::normalize('$string', false|true) === '$expected'
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testNormalizeMethod(string $string, string $expected): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UBase64::normalize($string, $no_throw));
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
	 * Test <code>normalize</code> method expecting an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @dataProvider provideNormalizeMethodDataForInvalidStringException
	 * @testdox Base64::normalize('$string') --> InvalidString exception
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @return void
	 */
	public function testNormalizeMethodInvalidStringException(string $string): void
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
	 * @dataProvider provideNormalizeMethodDataForInvalidStringException
	 * @testdox Base64::normalize('$string', true) === NULL
	 * 
	 * @param string $string
	 * <p>The method <var>$string</var> parameter to test with.</p>
	 * @return void
	 */
	public function testNormalizeMethodNoThrowNull(string $string): void
	{
		$this->assertNull(UBase64::normalize($string, true));
	}
	
	/**
	 * Provide <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>normalize</code> method data for an <code>InvalidString</code> exception to be thrown.</p>
	 */
	public function provideNormalizeMethodDataForInvalidStringException(): array
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
