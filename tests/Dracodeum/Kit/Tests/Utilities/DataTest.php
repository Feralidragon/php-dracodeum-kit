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
	
	/**
	 * Test <code>keyfy</code> method.
	 * 
	 * @dataProvider provideKeyfyMethodData
	 * @testdox Data::keyfy({$value}) === '$expected' (safe = $expected_safe)
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @param bool $expected_safe
	 * <p>The expected <var>$safe</var> reference parameter output value.</p>
	 * @return void
	 */
	public function testKeyfyMethod($value, string $expected, bool $expected_safe): void
	{
		foreach ([false, true] as $no_throw) {
			$this->assertSame($expected, UData::keyfy($value, $safe, $no_throw));
			$this->assertSame($expected_safe, $safe);
		}
	}
	
	/**
	 * Provide <code>keyfy</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>keyfy</code> method data.</p>
	 */
	public function provideKeyfyMethodData(): array
	{
		//initialize
		$object = new \stdClass();
		$resource = fopen(__FILE__, 'r');
		
		//return
		return [
			[null, 'n', true],
			[false, 'b:0', true],
			[true, 'b:1', true],
			[0, 'i:0', true],
			[123, 'i:123', true],
			[-962247, 'i:-962247', true],
			[0.0, 'f:0', true],
			[0.123, 'f:0.123', true],
			[248.0, 'f:248', true],
			[-76252.4589, 'f:-76252.4589', true],
			['foobar', 's:foobar', true],
			["Potatoes for sale.", 's:Potatoes for sale.', true],
			["The quick brown fox jumps over the lazy dog.", 'S:408d94384216f890ff7a0c3528e8bed1e0b01621', true],
			[$object, 'O:' . spl_object_id($object), false],
			[$resource, 'R:' . (int)$resource, false],
			[[], 'a:[]', true],
			[[null, true, 555], 'a:["n","b:1","i:555"]', true],
			[['a' => 0.1, ['foo']], 'a:{"a":"f:0.1","0":"a:[\\"s:foo\\"]"}', true],
			[['z' => 0.1, [$object]], 'a:{"z":"f:0.1","0":"a:[\\"O:' . spl_object_id($object) . '\\"]"}', false],
			[['w' => 0.125, 'b' => 'foo', 'bar' => ['zzzz']], 'A:c9d22572427ffc2c0c42d37c45263dac0b7a07ed', true]
		];
	}
}
