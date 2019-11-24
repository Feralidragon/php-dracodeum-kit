<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Data as UData;

/** @see \Dracodeum\Kit\Utilities\Data */
class DataTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>associative</code> method.
	 * 
	 * @dataProvider provideAssociativeMethodData
	 * @testdox Data::associative($array) === $expected
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testAssociativeMethod(array $array, bool $expected): void
	{
		$this->assertSame($expected, UData::associative($array));
	}
	
	/**
	 * Provide <code>associative</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>associative</code> method data.</p>
	 */
	public function provideAssociativeMethodData(): array
	{
		return [
			[[], false],
			[[1, 2, 77, 34], false],
			[[1, 2, 'bar', 'zzz'], false],
			[['a', 'foo', 'bar', 'zzz'], false],
			[['a' => 1, 'foo' => 2, 'bar' => 'zzz'], true],
			[['a' => 1, 2, 77, 34], true],
			[[1, 2, 77, 'a' => 34], true],
			[[1, 2, 'a' => 77, 34], true],
			[['a', 'foo', 'bar', 8 => 'zzz'], true],
			[[8 => 'a', 'foo', 'bar', 'zzz'], true],
			[['a', 'foo', 8 => 'bar', 'zzz'], true]
		];
	}
}
