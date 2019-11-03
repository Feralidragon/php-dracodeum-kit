<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Byte as UByte;
use Dracodeum\Kit\Utilities\Byte\Exceptions;

/** @see \Dracodeum\Kit\Utilities\Byte */
class ByteTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>hvalue</code> method.
	 * 
	 * @dataProvider provideHvalueMethodData
	 * @testdox Byte::hvalue($value, $options) === '$expected'
	 * 
	 * @param int $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param \Dracodeum\Kit\Utilities\Byte\Options\Hvalue|array|null $options
	 * <p>The method <var>$options</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testHvalueMethod(int $value, $options, string $expected): void
	{
		$this->assertSame($expected, UByte::hvalue($value, $options));
	}
	
	/**
	 * Provide <code>hvalue</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>hvalue</code> method data.</p>
	 */
	public function provideHvalueMethodData(): array
	{
		return [
			[0, null, '0 B'],
			[0, ['long' => true], '0 bytes'],
			[1, null, '1 B'],
			[1, ['long' => true], '1 byte'],
			[5, null, '5 B'],
			[5, ['long' => true], '5 bytes'],
			[1000, null, '1 kB'],
			[1000, ['long' => true], '1 kilobyte'],
			[5000, null, '5 kB'],
			[5000, ['long' => true], '5 kilobytes'],
			[1000000, null, '1 MB'],
			[1000000, ['long' => true], '1 megabyte'],
			[5000000, null, '5 MB'],
			[5000000, ['long' => true], '5 megabytes'],
			[1000000000, null, '1 GB'],
			[1000000000, ['long' => true], '1 gigabyte'],
			[5000000000, null, '5 GB'],
			[5000000000, ['long' => true], '5 gigabytes'],
			[1000000000000, null, '1 TB'],
			[1000000000000, ['long' => true], '1 terabyte'],
			[5000000000000, null, '5 TB'],
			[5000000000000, ['long' => true], '5 terabytes'],
			[1000000000000000, null, '1 PB'],
			[1000000000000000, ['long' => true], '1 petabyte'],
			[5000000000000000, null, '5 PB'],
			[5000000000000000, ['long' => true], '5 petabytes'],
			[1000000000000000000, null, '1 EB'],
			[1000000000000000000, ['long' => true], '1 exabyte'],
			[5000000000000000000, null, '5 EB'],
			[5000000000000000000, ['long' => true], '5 exabytes'],
			[39714, null, '39.71 kB'],
			[39714, ['long' => true], '39.71 kilobytes'],
			[39714, ['precision' => 3], '39.714 kB'],
			[39714, ['long' => true, 'precision' => 3], '39.714 kilobytes'],
			[39714, ['precision' => 1], '39.7 kB'],
			[39714, ['long' => true, 'precision' => 1], '39.7 kilobytes'],
			[39714, ['precision' => 0], '40 kB'],
			[39714, ['long' => true, 'precision' => 0], '40 kilobytes'],
			[39714, ['min_multiple' => 'MB'], '0.04 MB'],
			[39714, ['long' => true, 'min_multiple' => 'MB'], '0.04 megabytes'],
			[39714, ['precision' => 3, 'min_multiple' => 'MB'], '0.04 MB'],
			[39714, ['long' => true, 'precision' => 3, 'min_multiple' => 'MB'], '0.04 megabytes'],
			[39714, ['precision' => 5, 'min_multiple' => 'MB'], '0.03971 MB'],
			[39714, ['long' => true, 'precision' => 5, 'min_multiple' => 'MB'], '0.03971 megabytes'],
			[39714, ['max_multiple' => 'B'], '39714 B'],
			[39714, ['long' => true, 'max_multiple' => 'B'], '39714 bytes'],
			[-39714, null, '-39.71 kB'],
			[-39714, ['long' => true], '-39.71 kilobytes'],
			[-39714, ['precision' => 0], '-40 kB'],
			[-39714, ['long' => true, 'precision' => 0], '-40 kilobytes']
		];
	}
	
	/**
	 * Test <code>mvalue</code> method.
	 * 
	 * @dataProvider provideMvalueMethodData
	 * @testdox Byte::mvalue('$value') === $expected
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param int $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testMvalueMethod(string $value, int $expected): void
	{
		$this->assertSame($expected, UByte::mvalue($value));
		$this->assertSame($expected, UByte::mvalue($value, true));
	}
	
	/**
	 * Provide <code>mvalue</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>mvalue</code> method data.</p>
	 */
	public function provideMvalueMethodData(): array
	{
		return [
			['0', 0],
			['0 B', 0],
			['1', 1],
			['1B', 1],
			['1 B', 1],
			['1 byte', 1],
			['5', 5],
			['5B', 5],
			['5 B', 5],
			['5 bytes', 5],
			['1k', 1000],
			['1 kB', 1000],
			['1 kilobyte', 1000],
			['5k', 5000],
			['5 kB', 5000],
			['5 kilobytes', 5000],
			['1M', 1000000],
			['1 MB', 1000000],
			['1 megabyte', 1000000],
			['5M', 5000000],
			['5 MB', 5000000],
			['5 megabytes', 5000000],
			['1G', 1000000000],
			['1 GB', 1000000000],
			['1 gigabyte', 1000000000],
			['5G', 5000000000],
			['5 GB', 5000000000],
			['5 gigabytes', 5000000000],
			['1T', 1000000000000],
			['1 TB', 1000000000000],
			['1 terabyte', 1000000000000],
			['5T', 5000000000000],
			['5 TB', 5000000000000],
			['5 terabytes', 5000000000000],
			['1P', 1000000000000000],
			['1 PB', 1000000000000000],
			['1 petabyte', 1000000000000000],
			['5P', 5000000000000000],
			['5 PB', 5000000000000000],
			['5 petabytes', 5000000000000000],
			['1E', 1000000000000000000],
			['1 EB', 1000000000000000000],
			['1 exabyte', 1000000000000000000],
			['5E', 5000000000000000000],
			['5 EB', 5000000000000000000],
			['5 exabytes', 5000000000000000000],
			['39.7 kB', 39700],
			['39.7 kilobytes', 39700],
			['39.71 MB', 39710000],
			['39.71 megabytes', 39710000],
			['39.714 GB', 39714000000],
			['39.714 gigabytes', 39714000000],
			['0.039714 MB', 39714],
			['0.039714 megabytes', 39714],
			['+39.714 kB', 39714],
			['+39.714 kilobytes', 39714],
			['-39.714 kB', -39714],
			['-39.714 kilobytes', -39714],
			['-39,714 kB', -39714],
			['-39,714 kilobytes', -39714]
		];
	}
	
	/**
	 * Test <code>mvalue</code> method expecting an <code>InvalidValue</code> exception to be thrown.
	 * 
	 * @dataProvider provideMvalueMethodDataForInvalidValueException
	 * @testdox Byte::mvalue('$value') --> InvalidValue exception
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @return void
	 */
	public function testMvalueMethodInvalidValueException(string $value): void
	{
		$this->expectException(Exceptions\Mvalue\InvalidValue::class);
		UByte::mvalue($value);
	}
	
	/**
	 * Test <code>mvalue</code> method with <var>$no_throw</var> set to <code>true</code>, expecting <code>null</code>.
	 * 
	 * @dataProvider provideMvalueMethodDataForInvalidValueException
	 * @testdox Byte::mvalue('$value', true) === NULL
	 * 
	 * @param string $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @return void
	 */
	public function testMvalueMethodNoThrowNull(string $value): void
	{
		$this->assertNull(UByte::mvalue($value, true));
	}
	
	/**
	 * Provide <code>mvalue</code> method data for an <code>InvalidValue</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided <code>mvalue</code> method data for an <code>InvalidValue</code> exception to be thrown.</p>
	 */
	public function provideMvalueMethodDataForInvalidValueException(): array
	{
		return [
			[''],
			['.'],
			['abc'],
			['1m'],
			['1 mB'],
			['5 foobytes'],
			['--5 bytes'],
			['5_bytes'],
			['bytes 5']
		];
	}
}
