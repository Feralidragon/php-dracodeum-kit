<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
	 * @testdox Data::associative($array) === $expected
	 * @dataProvider provideAssociativeData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param bool $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testAssociative(array $array, bool $expected): void
	{
		$this->assertSame($expected, UData::associative($array));
	}
	
	/**
	 * Provide <code>associative</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>associative</code> method data.</p>
	 */
	public function provideAssociativeData(): array
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
	 * @testdox Data::keyfy({$value}) === '$expected' (safe = $expected_safe)
	 * @dataProvider provideKeyfyData
	 * 
	 * @param mixed $value
	 * <p>The method <var>$value</var> parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected method return value.</p>
	 * @param bool $expected_safe
	 * <p>The expected <var>$safe</var> reference parameter output value.</p>
	 * @return void
	 */
	public function testKeyfy($value, string $expected, bool $expected_safe): void
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
	public function provideKeyfyData(): array
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
	
	/**
	 * Test <code>merge</code> method.
	 * 
	 * @testdox Data::merge($array1, $array2, $depth, $flags) === $expected
	 * @dataProvider provideMergeData
	 * 
	 * @param array $array1
	 * <p>The method <var>$array1</var> parameter to test with.</p>
	 * @param array $array2
	 * <p>The method <var>$array2</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testMerge(array $array1, array $array2, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::merge($array1, $array2, $depth, $flags));
	}
	
	/**
	 * Provide <code>merge</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>merge</code> method data.</p>
	 */
	public function provideMergeData(): array
	{
		//initialize
		$array1 = [
			'a' => 'foo',
			'b' => 'bar',
			999 => [2, 5, 9, 2, 7, 5, 0],
			'd' => [0],
			'e' => null,
			'zed' => [
				'k1' => ['X', 'T'],
				'k2' => [],
				'k4' => [2 => 3, 3 => 3, 'k' => '#'],
				'k5' => ['foo', 'bar', 'f2b']
			],
			'farm' => [
				'carrots' => 100,
				'potatoes' => 2412,
				'cabages' => 'unknown'
			],
			'o' => [
				'bar',
				['a' => 11, 'b' => 4, 'c' => 1],
				[1, 5, 7],
				null
			]
		];
		$array2 = [
			'c' => 'f2b',
			'b' => 'unreal',
			997 => [0, 2, 3, 0, '0', 4, 2],
			999 => [5, 1],
			'd' => null,
			'e' => [111],
			'farm' => [
				'broccoli' => 73,
				'potatoes' => 1678,
				'carrots' => 125
			],
			'o' => [
				'foo',
				['a' => 1, 'b' => 5],
				[4, 5, 6, 0, 5],
				true
			],
			'zed' => [
				'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
				'k2' => 'foo2bar',
				'k3' => true,
				'k4' => [3, false, null],
				'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
			]
		];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array1, $array2, null, 0x00, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, 0x00, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, 0x00, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_ASSOC_UNION, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_ASSOC_UNION, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, 0, UData::MERGE_ASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, 1, UData::MERGE_ASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7, 0, 5],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_SWAP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_SWAP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_SWAP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_KEEP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_KEEP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_KEEP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'Z', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 0, UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k3' => true,
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar']
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, 1, UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [3, false, null],
					'k5' => [1 => 'Y', 0 => 227, 3 => 'f2b', 2 => 'foobar'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_ASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0, 5, 1],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 11, 'b' => 4, 'c' => 1],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 0, 5],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 11, 'b' => 4, 'c' => 1],
					[4, 5, 6],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_SWAP, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_KEEP, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 7, 0, 1],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'Z', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7, 0, 5],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_SWAP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_KEEP, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'Y', 'F', 'Z', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null,
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_ASSOC | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 6 => 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 5 => 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6, 0],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_UNION | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T', 'Z', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7, 0],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_LEFT | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1, 9, 2, 7, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5, 'c' => 1],
					[4, 5, 6],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_SWAP | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [5, 1],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['Y', 'F', 'X', 'Z', 'K'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'foo',
					['a' => 1, 'b' => 5],
					[4, 5, 6, 0, 5],
					true
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_NONASSOC_KEEP | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => [227, 'Y', 'foobar', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_ASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 0, 5],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_NONASSOC_LEFT, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_ASSOC_UNION, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 11, 'b' => 4, 'c' => 1],
					[4, 5, 6],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_NONASSOC_UNION, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_LEFT, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 7, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'Z', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 0],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_NONASSOC_LEFT | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 7, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1, 9, 2, 7, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 11, 'b' => 4, 'c' => 1],
					[4, 5, 6],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_NONASSOC_UNION | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 7, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_LEFT | UData::MERGE_NONASSOC_UNIQUE, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 7, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T', 'X', 'Z', 'X', 'K'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 0, 5],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_NONASSOC_LEFT | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
					'k5' => ['foo', 'bar', 'f2b', 'f2b'],
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				],
				'c' => 'f2b',
				997 => [0, 2, 3, 0, '0', 4, 2]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [5, 1, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['Y', 'F'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'foo',
					['a' => 11, 'b' => 4, 'c' => 1],
					[4, 5, 6],
					true
				]
			]],
			[$array1, $array2, null, UData::MERGE_LEFT | UData::MERGE_NONASSOC_UNION | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'unreal',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => null,
				'e' => [111],
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => 'foo2bar',
					'k4' => [2 => null, 3 => 3, 'k' => '#'],
					'k5' => [227, 'Y', 'foobar']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 1, 'b' => 5, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, UData::MERGE_UNION | UData::MERGE_LEFT | UData::MERGE_NONASSOC_ASSOC, [
				'a' => 'foo',
				'b' => 'bar',
				999 => [2, 5, 9, 2, 7, 5, 0],
				'd' => [0],
				'e' => null,
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [],
					'k4' => [2 => 3, 3 => 3, 'k' => '#'],
					'k5' => ['foo', 'bar', 'f2b']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7],
					null
				]
			]],
			[$array1, $array2, null, 
				UData::MERGE_UNION | UData::MERGE_ASSOC_LEFT | UData::MERGE_NONASSOC_ASSOC | 
				UData::MERGE_NONASSOC_UNIQUE, [
					'a' => 'foo',
					'b' => 'bar',
					999 => [2, 5, 9, 4 => 7, 6 => 0],
					'd' => [0],
					'e' => null,
					'zed' => [
						'k1' => ['X', 'T', 3 => 'Z', 5 => 'K'],
						'k2' => [],
						'k4' => [2 => 3, 3 => 3, 'k' => '#'],
						'k5' => ['foo', 'bar', 'f2b']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
					],
					'o' => [
						'bar',
						['a' => 11, 'b' => 4, 'c' => 1],
						[1, 5, 7, 0],
						null
					]
				]
			],
			[$array1, $array2, null,
				UData::MERGE_UNION | UData::MERGE_NONASSOC_LEFT | UData::MERGE_NONASSOC_ASSOC | 
				UData::MERGE_NONASSOC_UNIQUE, [
					'a' => 'foo',
					'b' => 'bar',
					999 => [2, 5, 9, 4 => 7, 6 => 0],
					'd' => [0],
					'e' => null,
					'zed' => [
						'k1' => ['X', 'T'],
						'k2' => [],
						'k4' => [2 => 3, 3 => 3, 'k' => '#', 0 => 3, 1 => false],
						'k5' => ['foo', 'bar', 'f2b', 'f2b'],
						'k3' => true
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown',
						'broccoli' => 73
					],
					'o' => [
						'bar',
						['a' => 11, 'b' => 4, 'c' => 1],
						[1, 5, 7],
						null
					],
					'c' => 'f2b',
					997 => [0, 2, 3, 0, '0', 4, 2]
				]
			],
			[$array1, $array2, null,
				UData::MERGE_LEFT | UData::MERGE_ASSOC_UNION | UData::MERGE_NONASSOC_ASSOC | 
				UData::MERGE_NONASSOC_UNIQUE, [
					'a' => 'foo',
					'b' => 'bar',
					999 => [5, 1, 9, 2, 7, 6 => 0],
					'd' => [0],
					'e' => null,
					'zed' => [
						'k1' => ['Y', 'F'],
						'k2' => [],
						'k4' => [2 => 3, 3 => 3, 'k' => '#'],
						'k5' => ['foo', 'bar', 'f2b']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
					],
					'o' => [
						'foo',
						['a' => 11, 'b' => 4, 'c' => 1],
						[4, 5, 6],
						true
					]
				]
			],
			[$array1, $array2, null,
				UData::MERGE_LEFT | UData::MERGE_NONASSOC_UNION | UData::MERGE_NONASSOC_ASSOC | 
				UData::MERGE_NONASSOC_UNIQUE, [
					'a' => 'foo',
					'b' => 'unreal',
					999 => [2, 5, 9, 4 => 7, 6 => 0],
					'd' => null,
					'e' => [111],
					'zed' => [
						'k1' => ['X', 'T'],
						'k2' => 'foo2bar',
						'k4' => [2 => null, 3 => 3, 'k' => '#'],
						'k5' => [227, 'Y', 'foobar']
					],
					'farm' => [
						'carrots' => 125,
						'potatoes' => 1678,
						'cabages' => 'unknown'
					],
					'o' => [
						'bar',
						['a' => 1, 'b' => 5, 'c' => 1],
						[1, 5, 7],
						null
					]
				]
			],
			[$array1, $array2, null,
				UData::MERGE_UNION | UData::MERGE_LEFT | UData::MERGE_NONASSOC_ASSOC | 
				UData::MERGE_NONASSOC_UNIQUE, [
					'a' => 'foo',
					'b' => 'bar',
					999 => [2, 5, 9, 4 => 7, 6 => 0],
					'd' => [0],
					'e' => null,
					'zed' => [
						'k1' => ['X', 'T'],
						'k2' => [],
						'k4' => [2 => 3, 3 => 3, 'k' => '#'],
						'k5' => ['foo', 'bar', 'f2b']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
					],
					'o' => [
						'bar',
						['a' => 11, 'b' => 4, 'c' => 1],
						[1, 5, 7],
						null
					]
				]
			]
		];
	}
	
	/**
	 * Test <code>unique</code> method.
	 * 
	 * @testdox Data::unique($array, $depth, $flags) === $expected
	 * @dataProvider provideUniqueData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testUnique(array $array, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::unique($array, $depth, $flags));
	}
	
	/**
	 * Provide <code>unique</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>unique</code> method data.</p>
	 */
	public function provideUniqueData(): array
	{
		//initialize
		$array = [
			'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
			'b' => 'foobar',
			999 => 'unreal',
			'zed' => [
				'k1' => ['X', 'T', 'T'],
				'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
				'k3' => ['X', 'X', 'T'],
				'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
				'k5' => [['j' => 111], ['j' => 111]]
			],
			997 => 'foobar',
			'farm' => [
				'carrots' => 100,
				'potatoes' => '100',
				'cabages' => 100,
				'broccoli' => 73
			],
			'c' => 'unreal',
			'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
			'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
			'o' => [
				'bar',
				777,
				false,
				['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
				[1, 5, 7, 5, '1', 0, 7],
				[1, 5, 5, 1, 7, 5, '1', 0, 7],
				null,
				false,
				'0',
				777
			]
		];
		
		//return
		return [
			[[], null, 0x00, []],
			[$array, null, 0x00, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, 0, 0x00, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, 0x00, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, 0, UData::UNIQUE_ASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_ASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_ASSOC, [
				'a' => [
					5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => [], 15 => []
				],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 3 => 'T', 5 => 0, 7 => 1, 8 => null],
					'k3' => ['X', 2 => 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 4 => '1', 5 => 0],
					[1, 5, 4 => 7, 6 => '1', 7 => 0],
					null,
					8 => '0'
				]
			]],
			[$array, 0, UData::UNIQUE_NONASSOC_ASSOC, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_NONASSOC_ASSOC, [
				'a' => [
					5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => [], 15 => []
				],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					8 => '0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 0, UData::UNIQUE_NONASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_NONASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, 0, UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, 0, UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_ARRAYS_AS_VALUES, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, 0, UData::UNIQUE_ARRAYS_AS_VALUES, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, 1, UData::UNIQUE_ARRAYS_AS_VALUES, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					'0'
				]
			]],
			[$array, 2, UData::UNIQUE_ARRAYS_AS_VALUES, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE | UData::UNIQUE_NONASSOC_ASSOC, [
				'a' => [
					5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => [], 15 => []
				],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 3 => 'T', 5 => 0, 7 => 1, 8 => null],
					'k3' => ['X', 2 => 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 4 => '1', 5 => 0],
					[1, 5, 4 => 7, 6 => '1', 7 => 0],
					null,
					8 => '0'
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE | UData::UNIQUE_NONASSOC_EXCLUDE, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE | UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, [], []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, '1', 0],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE | UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_ASSOC_EXCLUDE | UData::UNIQUE_ARRAYS, [
				'a' => [5, 3, 0, '0', 7, '7', 7.0, null, false, true, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 'T', 0, 1, null],
					'k3' => ['X', 'T'],
					'k4' => [2 => 3, 3 => 3, 'k' => '#', 555 => 1, 777 => '#'],
					'k5' => [['j' => 111]]
				],
				997 => 'foobar',
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 100,
					'broccoli' => 73
				],
				'c' => 'unreal',
				'd' => ['x' => 333, 'y' => 'u', 'z' => 333],
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'd' => 11, 'e' => '4', 'f' => 1],
					[1, 5, 7, '1', 0],
					null,
					'0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_ASSOC | UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 3 => 'T', 5 => 0, 7 => 1, 8 => null],
					'k3' => ['X', 2 => 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 4 => '1', 5 => 0],
					[1, 5, 4 => 7, 6 => '1', 7 => 0],
					null,
					8 => '0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_ASSOC | UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [
					5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => [], 15 => []
				],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 3 => 'T', 5 => 0, 7 => 1, 8 => null],
					'k3' => ['X', 2 => 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 4 => '1', 5 => 0],
					[1, 5, 4 => 7, 6 => '1', 7 => 0],
					null,
					8 => '0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_ASSOC | UData::UNIQUE_ARRAYS, [
				'a' => [5, 3, 0, '0', 5 => 7, 6 => '7', 7 => 7.0, 8 => null, 9 => false, 10 => true, 13 => []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T'],
					'k2' => [true, 'X', 3 => 'T', 5 => 0, 7 => 1, 8 => null],
					'k3' => ['X', 2 => 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 4 => '1', 5 => 0],
					[1, 5, 4 => 7, 6 => '1', 7 => 0],
					null,
					8 => '0'
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_EXCLUDE | UData::UNIQUE_ASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_EXCLUDE | UData::UNIQUE_NONASSOC_ARRAYS, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'e' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]],
			[$array, null, UData::UNIQUE_NONASSOC_EXCLUDE | UData::UNIQUE_ARRAYS, [
				'a' => [5, 3, 0, '0', 3, 7, '7', 7.0, null, false, true, false, '0', [], 5, []],
				'b' => 'foobar',
				999 => 'unreal',
				'zed' => [
					'k1' => ['X', 'T', 'T'],
					'k2' => [true, 'X', 'X', 'T', true, 0, true, 1, null, 0],
					'k3' => ['X', 'X', 'T'],
					'k4' => [2 => 3, 'k' => '#', 555 => 1],
					'k5' => [['j' => 111], ['j' => 111]]
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'd' => ['x' => 333, 'y' => 'u'],
				'o' => [
					'bar',
					777,
					false,
					['a' => 11, 'b' => 4, 'c' => 1, 'e' => '4'],
					[1, 5, 7, 5, '1', 0, 7],
					[1, 5, 5, 1, 7, 5, '1', 0, 7],
					null,
					false,
					'0',
					777
				]
			]]
		];
	}
	
	/**
	 * Test <code>sort</code> method.
	 * 
	 * @testdox Data::sort($array, $depth, $flags) === $expected
	 * @dataProvider provideSortData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testSort(array $array, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::sort($array, $depth, $flags));
	}
	
	/**
	 * Provide <code>sort</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>sort</code> method data.</p>
	 */
	public function provideSortData(): array
	{
		//initialize
		$array = [
			'a' => [4, 66, 1, -6, 0, 73, 1, 20],
			'b' => 'foo',
			997 => 810,
			'c' => [
				'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
				'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
				'k4' => 'unreal',
				'k5' => 'foobar',
				'k2' => null
			],
			999 => 700,
			'farm' => [
				'carrots' => 100,
				'broccoli' => 73,
				'cabages' => 240,
				'potatoes' => '100'
			],
			'd' => 'bar',
			711 => 750
		];
		
		//return
		return [
			[[], null, 0x00, []],
			[$array, null, 0x00, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => ['k' => '#', 555 => 1, 2 => 3, 'X' => 'T'],
					'k1' => ['A', 'Ai', 'K', 'U', 'Ua', 'a', 'b', 'j', 'z']
				],
				'a' => [-6, 0, 1, 1, 4, 20, 66, 73]
			]],
			[$array, 0, 0x00, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20]
			]],
			[$array, 1, 0x00, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai']
				],
				'a' => [-6, 0, 1, 1, 4, 20, 66, 73]
			]],
			[$array, null, UData::SORT_REVERSE, [
				'a' => [73, 66, 20, 4, 1, 1, 0, -6],
				'c' => [
					'k1' => ['z', 'j', 'b', 'a', 'Ua', 'U', 'K', 'Ai', 'A'],
					'k7' => ['X' => 'T', 2 => 3, 555 => 1, 'k' => '#'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'b' => 'foo',
				'd' => 'bar',
				997 => 810,
				711 => 750,
				999 => 700
			]],
			[$array, 0, UData::SORT_REVERSE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'b' => 'foo',
				'd' => 'bar',
				997 => 810,
				711 => 750,
				999 => 700
			]],
			[$array, 1, UData::SORT_REVERSE, [
				'a' => [73, 66, 20, 4, 1, 1, 0, -6],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'b' => 'foo',
				'd' => 'bar',
				997 => 810,
				711 => 750,
				999 => 700
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [-6, 0, 1, 1, 4, 20, 66, 73],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => ['A', 'Ai', 'K', 'U', 'Ua', 'a', 'b', 'j', 'z'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, 0, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, 1, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [-6, 0, 1, 1, 4, 20, 66, 73],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, null, UData::SORT_NONASSOC_ASSOC, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => ['k' => '#', 555 => 1, 2 => 3, 'X' => 'T'],
					'k1' => [2 => 'A', 8 => 'Ai', 4 => 'K', 5 => 'U', 6 => 'Ua', 1 => 'a', 0 => 'b', 7 => 'j', 3 => 'z']
				],
				'a' => [3 => -6, 4 => 0, 2 => 1, 6 => 1, 0 => 4, 7 => 20, 1 => 66, 5 => 73]
			]],
			[$array, 0, UData::SORT_NONASSOC_ASSOC, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20]
			]],
			[$array, 1, UData::SORT_NONASSOC_ASSOC, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai']
				],
				'a' => [3 => -6, 4 => 0, 2 => 1, 6 => 1, 0 => 4, 7 => 20, 1 => 66, 5 => 73]
			]],
			[$array, null, UData::SORT_NONASSOC_EXCLUDE, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => ['k' => '#', 555 => 1, 2 => 3, 'X' => 'T'],
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai']
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20]
			]],
			[$array, 0, UData::SORT_NONASSOC_EXCLUDE, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20]
			]],
			[$array, 1, UData::SORT_NONASSOC_EXCLUDE, [
				999 => 700,
				711 => 750,
				997 => 810,
				'd' => 'bar',
				'b' => 'foo',
				'farm' => [
					'broccoli' => 73,
					'carrots' => 100,
					'potatoes' => '100',
					'cabages' => 240
				],
				'c' => [
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai']
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20]
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE, [
				'a' => [73, 66, 20, 4, 1, 1, 0, -6],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => ['z', 'j', 'b', 'a', 'Ua', 'U', 'K', 'Ai', 'A'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_ASSOC, [
				'a' => [5 => 73, 1 => 66, 7 => 20, 0 => 4, 2 => 1, 6 => 1, 4 => 0, 3 => -6],
				'c' => [
					'k1' => [
						3 => 'z', 7 => 'j', 0 => 'b', 1 => 'a', 6 => 'Ua', 5 => 'U', 4 => 'K', 8 => 'Ai', 2 => 'A'
					],
					'k7' => ['X' => 'T', 2 => 3, 555 => 1, 'k' => '#'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'b' => 'foo',
				'd' => 'bar',
				997 => 810,
				711 => 750,
				999 => 700
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => ['X' => 'T', 2 => 3, 555 => 1, 'k' => '#'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100',
					'broccoli' => 73
				],
				'b' => 'foo',
				'd' => 'bar',
				997 => 810,
				711 => 750,
				999 => 700
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'a' => [3 => -6, 4 => 0, 2 => 1, 6 => 1, 0 => 4, 7 => 20, 1 => 66, 5 => 73],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [
						2 => 'A', 8 => 'Ai', 4 => 'K', 5 => 'U', 6 => 'Ua', 1 => 'a', 0 => 'b', 7 => 'j', 3 => 'z'
					],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => ['b', 'a', 'A', 'z', 'K', 'U', 'Ua', 'j', 'Ai'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'a' => [5 => 73, 1 => 66, 7 => 20, 0 => 4, 2 => 1, 6 => 1, 4 => 0, 3 => -6],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [
						3 => 'z', 7 => 'j', 0 => 'b', 1 => 'a', 6 => 'Ua', 5 => 'U', 4 => 'K', 8 => 'Ai', 2 => 'A'
					],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]]
		];
	}
	
	/**
	 * Test <code>ksort</code> method.
	 * 
	 * @testdox Data::ksort($array, $depth, $flags) === $expected
	 * @dataProvider provideKsortData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testKsort(array $array, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::ksort($array, $depth, $flags));
	}
	
	/**
	 * Provide <code>ksort</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>ksort</code> method data.</p>
	 */
	public function provideKsortData(): array
	{
		//initialize
		$array = [
			'b' => 'foo',
			'd' => 'bar',
			'c' => [
				'k1' => ['b', 'a'],
				'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
				'k4' => 'unreal',
				'k5' => 'foobar',
				'k2' => null
			],
			'a' => [4, 66, 1],
			997 => 810,
			999 => 700,
			'farm' => [
				'carrots' => 100,
				'broccoli' => 73,
				'cabages' => 240
			],
			711 => 750
		];
		
		//return
		return [
			[[], null, 0x00, []],
			[$array, null, 0x00, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => ['X' => 'T', 'k' => '#', 2 => 3, 555 => 1]
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 0, 0x00, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 1, 0x00, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T']
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, null, UData::SORT_REVERSE, [
				999 => 700,
				997 => 810,
				711 => 750,
				'farm' => [
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				'd' => 'bar',
				'c' => [
					'k7' => [555 => 1, 2 => 3, 'k' => '#', 'X' => 'T'],
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k2' => null,
					'k1' => ['a', 'b']
				],
				'b' => 'foo',
				'a' => [1, 66, 4]
			]],
			[$array, 0, UData::SORT_REVERSE, [
				999 => 700,
				997 => 810,
				711 => 750,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'b' => 'foo',
				'a' => [4, 66, 1]
			]],
			[$array, 1, UData::SORT_REVERSE, [
				999 => 700,
				997 => 810,
				711 => 750,
				'farm' => [
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				'd' => 'bar',
				'c' => [
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k2' => null,
					'k1' => ['b', 'a']
				],
				'b' => 'foo',
				'a' => [1, 66, 4]
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, 0, UData::SORT_ASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, 1, UData::SORT_ASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, null, UData::SORT_NONASSOC_ASSOC, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => ['X' => 'T', 'k' => '#', 2 => 3, 555 => 1]
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 0, UData::SORT_NONASSOC_ASSOC, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 1, UData::SORT_NONASSOC_ASSOC, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T']
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, null, UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => ['X' => 'T', 'k' => '#', 2 => 3, 555 => 1]
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 0, UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, 1, UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1],
				'b' => 'foo',
				'c' => [
					'k1' => ['b', 'a'],
					'k2' => null,
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T']
				],
				'd' => 'bar',
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100
				],
				711 => 750,
				997 => 810,
				999 => 700
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['a', 'b'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [1, 66, 4],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_ASSOC, [
				999 => 700,
				997 => 810,
				711 => 750,
				'farm' => [
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				'd' => 'bar',
				'c' => [
					'k7' => [555 => 1, 2 => 3, 'k' => '#', 'X' => 'T'],
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k2' => null,
					'k1' => [1 => 'a', 0 => 'b']
				],
				'b' => 'foo',
				'a' => [2 => 1, 1 => 66, 0 => 4]
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_EXCLUDE, [
				999 => 700,
				997 => 810,
				711 => 750,
				'farm' => [
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				'd' => 'bar',
				'c' => [
					'k7' => [555 => 1, 2 => 3, 'k' => '#', 'X' => 'T'],
					'k5' => 'foobar',
					'k4' => 'unreal',
					'k2' => null,
					'k1' => ['b', 'a']
				],
				'b' => 'foo',
				'a' => [4, 66, 1]
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => ['b', 'a'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]],
			[$array, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'b' => 'foo',
				'd' => 'bar',
				'c' => [
					'k1' => [1 => 'a', 0 => 'b'],
					'k7' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [2 => 1, 1 => 66, 0 => 4],
				997 => 810,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240
				],
				711 => 750
			]]
		];
	}
	
	/**
	 * Test <code>fsort</code> method.
	 * 
	 * @testdox Data::fsort($array, $comparer, $depth, $flags) === $expected
	 * @dataProvider provideFsortData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param callable $comparer
	 * <p>The method <var>$comparer</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testFsort(array $array, callable $comparer, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::fsort($array, $comparer, $depth, $flags));
	}
	
	/**
	 * Provide <code>fsort</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>fsort</code> method data.</p>
	 */
	public function provideFsortData(): array
	{
		//initialize
		$array = [
			'a' => [4, 66, 1, -6, 0, 73, 1, 20],
			'b' => 'foo',
			997 => 810,
			'c' => [
				'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
				2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
				'k4' => 'unreal',
				'k5' => 'foobar',
				'k2' => null
			],
			999 => 700,
			'farm' => [
				'carrots' => 100,
				'broccoli' => 73,
				'cabages' => 240,
				'potatoes' => '100'
			],
			'd' => 'bar',
			711 => 750
		];
		$comparer = function ($key1, $value1, $key2, $value2): int {
			//keys
			if (gettype($key1) !== gettype($key2)) {
				return is_int($key1) ? -1 : 1;
			}
			
			//values
			$type1 = strtolower(gettype($value1));
			$type2 = strtolower(gettype($value2));
			if ($type1 !== $type2) {
				return strcmp($type1, $type2);
			} elseif ($value1 === null) {
				return 0;
			} elseif (is_array($value1)) {
				return count($value1) - count($value2);
			}
			return is_string($value1) ? strcmp($value1, $value2) : -strcmp($value1, $value2);
		};
		
		//return
		return [
			[[], $comparer, null, 0x00, []],
			[$array, $comparer, null, 0x00, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => ['A', 'K', 'U', 'a', 'b', 'z'],
					'k1' => [2 => 3, 555 => 1, 'k' => '#', 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [73, 66, 4, 20, 1, 1, 0, -6],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 0, 0x00, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 1, 0x00, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [73, 66, 4, 20, 1, 1, 0, -6],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, null, UData::SORT_REVERSE, [
				'b' => 'foo',
				'd' => 'bar',
				'a' => [-6, 0, 1, 1, 20, 4, 66, 73],
				'c' => [
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null,
					'k1' => ['X' => 'T', 'k' => '#', 555 => 1, 2 => 3],
					2456 => ['z', 'b', 'a', 'U', 'K', 'A']
				],
				'farm' => [
					'potatoes' => '100',
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				999 => 700,
				711 => 750,
				997 => 810
			]],
			[$array, $comparer, 0, UData::SORT_REVERSE, [
				'b' => 'foo',
				'd' => 'bar',
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				999 => 700,
				711 => 750,
				997 => 810
			]],
			[$array, $comparer, 1, UData::SORT_REVERSE, [
				'b' => 'foo',
				'd' => 'bar',
				'a' => [-6, 0, 1, 1, 20, 4, 66, 73],
				'c' => [
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null,
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U']
				],
				'farm' => [
					'potatoes' => '100',
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				999 => 700,
				711 => 750,
				997 => 810
			]],
			[$array, $comparer, null, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [73, 66, 4, 20, 1, 1, 0, -6],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['A', 'K', 'U', 'a', 'b', 'z'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, 0, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, 1, UData::SORT_ASSOC_EXCLUDE, [
				'a' => [73, 66, 4, 20, 1, 1, 0, -6],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, null, UData::SORT_NONASSOC_ASSOC, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => [2 => 'A', 4 => 'K', 5 => 'U', 1 => 'a', 0 => 'b', 3 => 'z'],
					'k1' => [2 => 3, 555 => 1, 'k' => '#', 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [5 => 73, 1 => 66, 0 => 4, 7 => 20, 2 => 1, 6 => 1, 4 => 0, 3 => -6],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 0, UData::SORT_NONASSOC_ASSOC, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 1, UData::SORT_NONASSOC_ASSOC, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [5 => 73, 1 => 66, 0 => 4, 7 => 20, 2 => 1, 6 => 1, 4 => 0, 3 => -6],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, null, UData::SORT_NONASSOC_EXCLUDE, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k1' => [2 => 3, 555 => 1, 'k' => '#', 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 0, UData::SORT_NONASSOC_EXCLUDE, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, 1, UData::SORT_NONASSOC_EXCLUDE, [
				997 => 810,
				711 => 750,
				999 => 700,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 240,
					'carrots' => 100,
					'potatoes' => '100'
				],
				'c' => [
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					'k2' => null,
					'k5' => 'foobar',
					'k4' => 'unreal'
				],
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'd' => 'bar',
				'b' => 'foo'
			]],
			[$array, $comparer, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE, [
				'a' => [-6, 0, 1, 1, 20, 4, 66, 73],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['z', 'b', 'a', 'U', 'K', 'A'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_ASSOC, [
				'b' => 'foo',
				'd' => 'bar',
				'a' => [3 => -6, 4 => 0, 2 => 1, 6 => 1, 7 => 20, 0 => 4, 1 => 66, 5 => 73],
				'c' => [
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null,
					'k1' => ['X' => 'T', 'k' => '#', 555 => 1, 2 => 3],
					2456 => [3 => 'z', 0 => 'b', 1 => 'a', 5 => 'U', 4 => 'K', 2 => 'A']
				],
				'farm' => [
					'potatoes' => '100',
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				999 => 700,
				711 => 750,
				997 => 810
			]],
			[$array, $comparer, null, UData::SORT_REVERSE | UData::SORT_NONASSOC_EXCLUDE, [
				'b' => 'foo',
				'd' => 'bar',
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'c' => [
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null,
					'k1' => ['X' => 'T', 'k' => '#', 555 => 1, 2 => 3],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U']
				],
				'farm' => [
					'potatoes' => '100',
					'carrots' => 100,
					'cabages' => 240,
					'broccoli' => 73
				],
				999 => 700,
				711 => 750,
				997 => 810
			]],
			[$array, $comparer, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'a' => [5 => 73, 1 => 66, 0 => 4, 7 => 20, 2 => 1, 6 => 1, 4 => 0, 3 => -6],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => [2 => 'A', 4 => 'K', 5 => 'U', 1 => 'a', 0 => 'b', 3 => 'z'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, null, UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_EXCLUDE, [
				'a' => [4, 66, 1, -6, 0, 73, 1, 20],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => ['b', 'a', 'A', 'z', 'K', 'U'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]],
			[$array, $comparer, null, UData::SORT_REVERSE | UData::SORT_ASSOC_EXCLUDE | UData::SORT_NONASSOC_ASSOC, [
				'a' => [3 => -6, 4 => 0, 2 => 1, 6 => 1, 7 => 20, 0 => 4, 1 => 66, 5 => 73],
				'b' => 'foo',
				997 => 810,
				'c' => [
					'k1' => [2 => 3, 'k' => '#', 555 => 1, 'X' => 'T'],
					2456 => [3 => 'z', 0 => 'b', 1 => 'a', 5 => 'U', 4 => 'K', 2 => 'A'],
					'k4' => 'unreal',
					'k5' => 'foobar',
					'k2' => null
				],
				999 => 700,
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73,
					'cabages' => 240,
					'potatoes' => '100'
				],
				'd' => 'bar',
				711 => 750
			]]
		];
	}
	
	/**
	 * Test <code>filter</code> method.
	 * 
	 * @testdox Data::filter($array, $values, $depth, $flags) === $expected
	 * @dataProvider provideFilterData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param array $values
	 * <p>The method <var>$values</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testFilter(array $array, array $values, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::filter($array, $values, $depth, $flags));
	}
	
	/**
	 * Provide <code>filter</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>filter</code> method data.</p>
	 */
	public function provideFilterData(): array
	{
		//initialize
		$array = [
			'a' => 123,
			'b' => 'foo',
			777 => ['bar', 'f2b', null, false, true],
			997 => '123',
			'c' => [
				'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
				'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
				'k3' => 'unreal',
				'k4' => 'f2b',
				'k5' => ['foo', true],
				'k6' => ['x' => '100'],
				'k7' => null
			],
			'd' => 'bar',
			999 => 'f2b',
			'e' => null,
			'farm' => [
				'carrots' => 100,
				'cabages' => 123,
				'broccoli' => 73,
				'potatoes' => '100'
			],
			'f' => ['bar', 'X', 100],
			'g' => false,
			'h' => true
		];
		$values = [123, 'foo', 'f2b', '100', null, true];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array, $values, null, 0x00, [
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100, false, 0],
					'k3' => 'unreal',
					'k5' => [],
					'k6' => [] 
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, 0x00, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, 0x00, [
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', null, true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', 123, true, null],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'f' => [],
				'h' => true
			]],
			[$array, $values, 0, UData::FILTER_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', null, false, true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'h' => true
			]],
			[$array, $values, 1, UData::FILTER_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', null, true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'f' => [],
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_EMPTY, [
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100, false, 0],
					'k3' => 'unreal'
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::FILTER_EMPTY, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::FILTER_EMPTY, [
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 0, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 1, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_NONASSOC_ASSOC, [
				777 => ['bar', 3 => false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 4 => 100, 8 => false, 9 => 0],
					'k3' => 'unreal',
					'k5' => [],
					'k6' => [] 
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::FILTER_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::FILTER_NONASSOC_ASSOC, [
				777 => ['bar', 3 => false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true],
					'k6' => [] 
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::FILTER_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::FILTER_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', null, true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', 123, true, null],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', null, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => [1 => 'f2b', 2 => null, 4 => true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => [1 => 'foo', 2 => 'f2b', 3 => '100', 5 => 123, 6 => true, 7 => null],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'f' => [],
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', null, false, true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_EMPTY | UData::FILTER_NONASSOC_ASSOC, [
				777 => ['bar', 3 => false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 4 => 100, 8 => false, 9 => 0],
					'k3' => 'unreal'
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_EMPTY | UData::FILTER_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k5' => ['foo', true]
				],
				'd' => 'bar',
				'farm' => [
					'carrots' => 100,
					'broccoli' => 73
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 3 => false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 4 => 100, 8 => false, 9 => 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', null, false, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 'foo', 'f2b', '100', 100, 123, true, null, false, 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', null, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => [1 => 'f2b', 2 => null, 4 => true],
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => [1 => 'foo', 2 => 'f2b', 3 => '100', 5 => 123, 6 => true, 7 => null],
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'cabages' => 123,
					'potatoes' => '100'
				],
				'h' => true
			]],
			[$array, $values, null, UData::FILTER_INVERSE | UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC,
				[
					'a' => 123,
					'b' => 'foo',
					777 => [1 => 'f2b', 2 => null, 4 => true],
					997 => '123',
					'c' => [
						'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
						'k2' => [1 => 'foo', 2 => 'f2b', 3 => '100', 5 => 123, 6 => true, 7 => null],
						'k3' => 'unreal',
						'k4' => 'f2b',
						'k5' => ['foo', true],
						'k6' => ['x' => '100'],
						'k7' => null
					],
					'd' => 'bar',
					999 => 'f2b',
					'e' => null,
					'farm' => [
						'carrots' => 100,
						'cabages' => 123,
						'broccoli' => 73,
						'potatoes' => '100'
					],
					'f' => [],
					'g' => false,
					'h' => true
				]
			],
			[$array, $values, null, UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 3 => false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 4 => 100, 8 => false, 9 => 0],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => 100,
					'cabages' => 123,
					'broccoli' => 73,
					'potatoes' => '100'
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null,
				UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE | 
				UData::FILTER_NONASSOC_ASSOC, [
					'a' => 123,
					'b' => 'foo',
					777 => [1 => 'f2b', 2 => null, 4 => true],
					997 => '123',
					'c' => [
						'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
						'k2' => [1 => 'foo', 2 => 'f2b', 3 => '100', 5 => 123, 6 => true, 7 => null],
						'k3' => 'unreal',
						'k4' => 'f2b',
						'k5' => ['foo', true],
						'k6' => ['x' => '100'],
						'k7' => null
					],
					'd' => 'bar',
					999 => 'f2b',
					'e' => null,
					'farm' => [
						'carrots' => 100,
						'cabages' => 123,
						'broccoli' => 73,
						'potatoes' => '100'
					],
					'g' => false,
					'h' => true
				]
			]
		];
	}
	
	/**
	 * Test <code>kfilter</code> method.
	 * 
	 * @testdox Data::kfilter($array, $keys, $depth, $flags) === $expected
	 * @dataProvider provideKfilterData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param array $keys
	 * <p>The method <var>$keys</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testKfilter(array $array, array $keys, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::kfilter($array, $keys, $depth, $flags));
	}
	
	/**
	 * Provide <code>kfilter</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>kfilter</code> method data.</p>
	 */
	public function provideKfilterData(): array
	{
		//initialize
		$array = [
			'a' => ['foo', 123, 5, true, null, 0, false],
			'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
			'foo' => 'unreal',
			'b' => 'f2b',
			'c' => [
				'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
				'k2' => [111, 'foo', 'b4r', '100'],
				'k3' => 'unreal',
				6722 => 'bar',
				'k4' => 'bar2foo',
				'k5' => ['foo', true],
				'k6' => ['x' => 'farm'],
				100 => null
			],
			'C' => [
				123 => 'foobar',
				'k8' => true,
				'x' => [true, 1, false, 0, null, '2b'],
				'k7' => null,
				1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
				0 => ['a' => 'b22', 'b' => 888]
			],
			667 => 'x1x2',
			123 => 'b2f',
			'd' => [false, 0],
			'x' => 11111,
			'e' => null,
			100 => [3 => true, 6 => 'U']
		];
		$keys = [123, 'foo', 'bar', '100', 0, 1, 5, 'x', 'k4', 'C'];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array, $keys, null, 0x00, [
				'a' => [5, true, null, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => [],
					'k6' => []
				],
				667 => 'x1x2',
				'd' => [],
				'e' => null
			]],
			[$array, $keys, 0, 0x00, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, 1, 0x00, [
				'a' => [5, true, null, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				667 => 'x1x2',
				'd' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_INVERSE, [
				'bar' => ['a', 'b', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null],
					0 => []
				],
				123 => 'b2f',
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, 0, UData::FILTER_INVERSE, [
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				123 => 'b2f',
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 1, UData::FILTER_INVERSE, [
				'bar' => ['a', 'b', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, false, 0, null, '2b'],
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				123 => 'b2f',
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::FILTER_EMPTY, [
				'a' => [5, true, null, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar'
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, 0, UData::FILTER_EMPTY, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, 1, UData::FILTER_EMPTY, [
				'a' => [5, true, null, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => [5, true, null, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 0, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [false, 0],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 1, UData::FILTER_ASSOC_EXCLUDE, [
				'a' => [5, true, null, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 6 => false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => [],
					'k6' => []
				],
				667 => 'x1x2',
				'd' => [],
				'e' => null
			]],
			[$array, $keys, 0, UData::FILTER_NONASSOC_ASSOC, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, 1, UData::FILTER_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 6 => false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				667 => 'x1x2',
				'd' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true],
					'k6' => []
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, 0, UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, 1, UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY, [
				'bar' => ['a', 'b', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null]
				],
				123 => 'b2f',
				'x' => 11111
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => ['foo', 123, 0],
				'bar' => ['a', 'b', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [false, 0],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_NONASSOC_ASSOC, [
				'bar' => ['a', 'b', 5 => 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, 5 => '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null],
					0 => []
				],
				123 => 'b2f',
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_NONASSOC_EXCLUDE, [
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, false, 0, null, '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null],
					0 => []
				],
				123 => 'b2f',
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => [5, true, null, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_EMPTY | UData::FILTER_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 6 => false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					6722 => 'bar'
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_EMPTY | UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k5' => ['foo', true]
				],
				667 => 'x1x2',
				'd' => [false, 0],
				'e' => null
			]],
			[$array, $keys, null, UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [false, 0],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE, [
				'a' => ['foo', 123, 0],
				'bar' => ['a', 'b', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [false, 0],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_NONASSOC_ASSOC, [
				'bar' => ['a', 'b', 5 => 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, 5 => '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null]
				],
				123 => 'b2f',
				'x' => 11111
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_NONASSOC_EXCLUDE, [
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'C' => [
					123 => 'foobar',
					'x' => [true, 1, false, 0, null, '2b'],
					1 => ['x' => 'K', 5 => 'U', 1 => null]
				],
				123 => 'b2f',
				'x' => 11111
			]],
			[$array, $keys, null, UData::FILTER_INVERSE | UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => ['foo', 123, 5 => 0],
				'bar' => ['a', 'b', 5 => 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, 5 => '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'd' => [false, 0],
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE | UData::FILTER_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					6722 => 'bar',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					123 => 'foobar',
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				123 => 'b2f',
				'x' => 11111,
				'e' => null,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null,
				UData::FILTER_INVERSE | UData::FILTER_EMPTY | UData::FILTER_ASSOC_EXCLUDE | 
				UData::FILTER_NONASSOC_ASSOC, [
					'a' => ['foo', 123, 5 => 0],
					'bar' => ['a', 'b', 5 => 'z'],
					'foo' => 'unreal',
					'b' => 'f2b',
					'c' => [
						'k1' => [2 => true, 'bar' => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
						'k2' => [111, 'foo'],
						'k3' => 'unreal',
						6722 => 'bar',
						'k4' => 'bar2foo',
						'k5' => ['foo', true],
						'k6' => ['x' => 'farm'],
						100 => null
					],
					'C' => [
						123 => 'foobar',
						'k8' => true,
						'x' => [true, 1, 5 => '2b'],
						'k7' => null,
						1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 1 => null],
						0 => ['a' => 'b22', 'b' => 888]
					],
					667 => 'x1x2',
					123 => 'b2f',
					'd' => [false, 0],
					'x' => 11111,
					'e' => null,
					100 => [3 => true, 6 => 'U']
				]
			]
		];
	}
	
	/**
	 * Test <code>trim</code> method.
	 * 
	 * @testdox Data::trim($array, $values, $depth, $flags) === $expected
	 * @dataProvider provideTrimData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param array $values
	 * <p>The method <var>$values</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testTrim(array $array, array $values, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::trim($array, $values, $depth, $flags));
	}
	
	/**
	 * Provide <code>trim</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>trim</code> method data.</p>
	 */
	public function provideTrimData(): array
	{
		//initialize
		$array = [
			'a' => 123,
			'b' => 'foo',
			777 => ['bar', 'f2b', false, null, true],
			997 => '123',
			'c' => [
				'k0' => [null],
				'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
				'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
				'k3' => 'unreal',
				'k4' => 'f2b',
				'k5' => ['foo', true],
				'k6' => ['x' => '100'],
				'k7' => null
			],
			'd' => 'bar',
			999 => 'f2b',
			'e' => null,
			'farm' => [
				'carrots' => '100',
				'broccoli' => 73,
				'cabages' => 123,
				'potatoes' => 100
			],
			'f' => ['bar', 'X', 100],
			'g' => false,
			'h' => true
		];
		$values = [123, 'foo', 'f2b', '100', null, true];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array, $values, null, 0x00, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, 0x00, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, 0x00, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 0, UData::TRIM_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 1, UData::TRIM_INVERSE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => [],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 0, UData::TRIM_LEFT, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 1, UData::TRIM_LEFT, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_RIGHT, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::TRIM_RIGHT, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::TRIM_RIGHT, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_EMPTY, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100],
					'k3' => 'unreal'
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::TRIM_EMPTY, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::TRIM_EMPTY, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 0, UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, 1, UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => ['X' => 'T'],
					'k2' => [3 => '123', 4 => 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::TRIM_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::TRIM_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => ['X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 0, UData::TRIM_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, 1, UData::TRIM_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100']
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_LEFT, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_RIGHT, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_EMPTY, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => [1 => 'f2b', 2 => false, 3 => null, 4 => true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'f' => [],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_RIGHT, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_EMPTY, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => ['123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => ['X' => 'T'],
					'k2' => [3 => '123', 4 => 100, 5 => 123, 6 => true, 7 => null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => [],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => ['X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => [],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_RIGHT | UData::TRIM_EMPTY, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100],
					'k3' => 'unreal'
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_RIGHT | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_RIGHT | UData::TRIM_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_RIGHT | UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => []
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_EMPTY | UData::TRIM_NONASSOC_ASSOC, [
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => ['X' => 'T'],
					'k2' => [3 => '123', 4 => 100],
					'k3' => 'unreal'
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_EMPTY | UData::TRIM_NONASSOC_EXCLUDE, [
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => ['X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true]
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false
			]],
			[$array, $values, null, UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k0' => [],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => [3 => '123', 4 => 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => [],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_EMPTY, [
				'a' => 123,
				'b' => 'foo',
				777 => ['f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_INVERSE | UData::TRIM_RIGHT | UData::TRIM_EMPTY, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k0' => [null],
					'k1' => [2 => true, 'k' => 'foo', 555 => 123],
					'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k5' => ['foo', true],
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123
				],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false, null, true],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['123', 100, 123, true, null],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_RIGHT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => ['foo', 'f2b', '100', '123', 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => 'foo',
				777 => ['bar', 'f2b', false],
				997 => '123',
				'c' => [
					'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
					'k2' => [3 => '123', 4 => 100],
					'k3' => 'unreal',
					'k4' => 'f2b',
					'k6' => ['x' => '100'],
					'k7' => null
				],
				'd' => 'bar',
				999 => 'f2b',
				'e' => null,
				'farm' => [
					'carrots' => '100',
					'broccoli' => 73,
					'cabages' => 123,
					'potatoes' => 100
				],
				'f' => ['bar', 'X', 100],
				'g' => false,
				'h' => true
			]],
			[$array, $values, null, 
				UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
					'a' => 123,
					'b' => 'foo',
					777 => [1 => 'f2b', 2 => false, 3 => null, 4 => true],
					997 => '123',
					'c' => [
						'k0' => [null],
						'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
						'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
						'k3' => 'unreal',
						'k4' => 'f2b',
						'k5' => ['foo', true],
						'k6' => ['x' => '100'],
						'k7' => null
					],
					'd' => 'bar',
					999 => 'f2b',
					'e' => null,
					'farm' => [
						'carrots' => '100',
						'broccoli' => 73,
						'cabages' => 123,
						'potatoes' => 100
					],
					'f' => [],
					'g' => false,
					'h' => true
				]
			],
			[$array, $values, null, 
				UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
					'a' => 123,
					'b' => 'foo',
					777 => ['bar', 'f2b', false, null, true],
					997 => '123',
					'c' => [
						'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
						'k2' => [3 => '123', 4 => 100, 5 => 123, 6 => true, 7 => null],
						'k3' => 'unreal',
						'k4' => 'f2b',
						'k6' => ['x' => '100'],
						'k7' => null
					],
					'd' => 'bar',
					999 => 'f2b',
					'e' => null,
					'farm' => [
						'carrots' => '100',
						'broccoli' => 73,
						'cabages' => 123,
						'potatoes' => 100
					],
					'f' => ['bar', 'X', 100],
					'g' => false,
					'h' => true
				]
			],
			[$array, $values, null, 
				UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | 
				UData::TRIM_NONASSOC_ASSOC, [
					'a' => 123,
					'b' => 'foo',
					777 => [1 => 'f2b', 2 => false, 3 => null, 4 => true],
					997 => '123',
					'c' => [
						'k0' => [null],
						'k1' => [2 => true, 'k' => 'foo', 555 => 123, 'X' => 'T'],
						'k2' => ['foo', 'f2b', '100', '123', 100, 123, true, null],
						'k3' => 'unreal',
						'k4' => 'f2b',
						'k5' => ['foo', true],
						'k6' => ['x' => '100'],
						'k7' => null
					],
					'd' => 'bar',
					999 => 'f2b',
					'e' => null,
					'farm' => [
						'carrots' => '100',
						'broccoli' => 73,
						'cabages' => 123,
						'potatoes' => 100
					],
					'g' => false,
					'h' => true
				]
			]
		];
	}
	
	/**
	 * Test <code>ktrim</code> method.
	 * 
	 * @testdox Data::ktrim($array, $keys, $depth, $flags) === $expected
	 * @dataProvider provideKtrimData
	 * 
	 * @param array $array
	 * <p>The method <var>$array</var> parameter to test with.</p>
	 * @param array $keys
	 * <p>The method <var>$keys</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testKtrim(array $array, array $keys, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::ktrim($array, $keys, $depth, $flags));
	}
	
	/**
	 * Provide <code>ktrim</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>ktrim</code> method data.</p>
	 */
	public function provideKtrimData(): array
	{
		//initialize
		$array = [
			123 => 'b2f',
			'a' => ['foo', 123, 5, true, null, 0, false],
			'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
			'foo' => 'unreal',
			'b' => 'f2b',
			'c' => [
				'k0' => [null],
				'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
				'k2' => [111, 'foo', 'b4r', '100'],
				'k3' => 'unreal',
				'k4' => 'bar2foo',
				'k5' => ['foo', true],
				'k6' => ['x' => 'farm'],
				100 => null
			],
			'C' => [
				5 => false,
				123 => 'foobar',
				'k8' => true,
				'x' => [true, 1, false, 0, null, '2b'],
				'k7' => null,
				1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
				0 => ['a' => 'b22', 'b' => 888]
			],
			667 => 'x1x2',
			'y' => ['k1' => null, 'bar' => 'X'],
			'e' => null,
			'x' => 11111,
			100 => [3 => true, 6 => 'U']
		];
		$keys = [123, 'foo', 'bar', '100', 0, 1, 5, 'x', 'k1'];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array, $keys, null, 0x00, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => []
				],
				'C' => [
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, 0, 0x00, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null
			]],
			[$array, $keys, 1, 0x00, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_INVERSE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U'],
					0 => []
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, 0, UData::TRIM_INVERSE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 1, UData::TRIM_INVERSE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::TRIM_LEFT, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => [],
					100 => null
				],
				'C' => [
					'k8' => true,
					'x' => [false, 0, null, '2b'],
					'k7' => null,
					1 => ['y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 0, UData::TRIM_LEFT, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 1, UData::TRIM_LEFT, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_RIGHT, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => []
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, 0, UData::TRIM_RIGHT, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null
			]],
			[$array, $keys, 1, UData::TRIM_RIGHT, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_EMPTY, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo'
				],
				'C' => [
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, 0, UData::TRIM_EMPTY, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null
			]],
			[$array, $keys, 1, UData::TRIM_EMPTY, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 0, UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, 1, UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => []
				],
				'C' => [
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, 0, UData::TRIM_NONASSOC_ASSOC, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null
			]],
			[$array, $keys, 1, UData::TRIM_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => []
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, 0, UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null
			]],
			[$array, $keys, 1, UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm']
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_LEFT, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => []
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_RIGHT, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U'],
					0 => []
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_EMPTY, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U']
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_NONASSOC_ASSOC, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U'],
					0 => []
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_NONASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U'],
					0 => []
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => []
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_RIGHT, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => []
				],
				'C' => [
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_EMPTY, [
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					100 => null
				],
				'C' => [
					'k8' => true,
					'x' => [false, 0, null, '2b'],
					'k7' => null,
					1 => ['y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y', 5 => 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => [],
					100 => null
				],
				'C' => [
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null, 5 => '2b'],
					'k7' => null,
					1 => ['y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => [],
					100 => null
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_RIGHT | UData::TRIM_EMPTY, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo'
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_RIGHT | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_RIGHT | UData::TRIM_NONASSOC_ASSOC, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => []
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_RIGHT | UData::TRIM_NONASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => []
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'y' => [],
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_EMPTY | UData::TRIM_NONASSOC_ASSOC, [
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo'
				],
				'C' => [
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_EMPTY | UData::TRIM_NONASSOC_EXCLUDE, [
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['k' => 'foo', 100 => 'U', 'X' => 'T'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true]
				],
				'C' => [
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null
				],
				667 => 'x1x2',
				'e' => null
			]],
			[$array, $keys, null, UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
				123 => 'b2f',
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => [],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_EMPTY, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111
			]],
			[$array, $keys, null, UData::TRIM_INVERSE | UData::TRIM_RIGHT | UData::TRIM_EMPTY, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0],
				'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k0' => [null],
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k5' => ['foo', true],
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U']
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111
			]],
			[$array, $keys, null, UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => [5, true, null, 0, false],
				'bar' => ['c', 'x', 'y', 'z'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => ['b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [false, 0, null, '2b'],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_RIGHT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE, [
				123 => 'b2f',
				'a' => ['foo', 123, 5, true, null, 0, false],
				'bar' => ['a', 'b', 'c', 'x', 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [111, 'foo', 'b4r', '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [true, 1, false, 0, null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null, UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
				123 => 'b2f',
				'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
				'bar' => [2 => 'c', 3 => 'x', 4 => 'y'],
				'foo' => 'unreal',
				'b' => 'f2b',
				'c' => [
					'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
					'k2' => [2 => 'b4r', 3 => '100'],
					'k3' => 'unreal',
					'k4' => 'bar2foo',
					'k6' => ['x' => 'farm'],
					100 => null
				],
				'C' => [
					5 => false,
					123 => 'foobar',
					'k8' => true,
					'x' => [2 => false, 3 => 0, 4 => null],
					'k7' => null,
					1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
					0 => ['a' => 'b22', 'b' => 888]
				],
				667 => 'x1x2',
				'y' => ['k1' => null, 'bar' => 'X'],
				'e' => null,
				'x' => 11111,
				100 => [3 => true, 6 => 'U']
			]],
			[$array, $keys, null,
				UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
					123 => 'b2f',
					'a' => ['foo', 123, 5, true, null, 0, false],
					'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
					'foo' => 'unreal',
					'b' => 'f2b',
					'c' => [
						'k0' => [null],
						'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
						'k2' => [111, 'foo', 'b4r', '100'],
						'k3' => 'unreal',
						'k4' => 'bar2foo',
						'k5' => ['foo', true],
						'k6' => ['x' => 'farm'],
						100 => null
					],
					'C' => [
						5 => false,
						123 => 'foobar',
						'k8' => true,
						'x' => [true, 1, false, 0, null, '2b'],
						'k7' => null,
						1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
						0 => ['a' => 'b22', 'b' => 888]
					],
					667 => 'x1x2',
					'y' => ['k1' => null, 'bar' => 'X'],
					'e' => null,
					'x' => 11111,
					100 => [3 => true, 6 => 'U']
				]
			],
			[$array, $keys, null,
				UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | UData::TRIM_NONASSOC_ASSOC, [
					123 => 'b2f',
					'a' => [2 => 5, 3 => true, 4 => null, 5 => 0, 6 => false],
					'bar' => [2 => 'c', 3 => 'x', 4 => 'y', 5 => 'z'],
					'foo' => 'unreal',
					'b' => 'f2b',
					'c' => [
						'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
						'k2' => [2 => 'b4r', 3 => '100'],
						'k3' => 'unreal',
						'k4' => 'bar2foo',
						'k6' => ['x' => 'farm'],
						100 => null
					],
					'C' => [
						5 => false,
						123 => 'foobar',
						'k8' => true,
						'x' => [2 => false, 3 => 0, 4 => null, 5 => '2b'],
						'k7' => null,
						1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
						0 => ['a' => 'b22', 'b' => 888]
					],
					667 => 'x1x2',
					'y' => ['k1' => null, 'bar' => 'X'],
					'e' => null,
					'x' => 11111,
					100 => [3 => true, 6 => 'U']
				]
			],
			[$array, $keys, null,
				UData::TRIM_INVERSE | UData::TRIM_LEFT | UData::TRIM_EMPTY | UData::TRIM_ASSOC_EXCLUDE | 
				UData::TRIM_NONASSOC_ASSOC, [
					123 => 'b2f',
					'a' => ['foo', 123, 5, true, null, 0, false],
					'bar' => ['a', 'b', 'c', 'x', 'y', 'z'],
					'foo' => 'unreal',
					'b' => 'f2b',
					'c' => [
						'k0' => [null],
						'k1' => ['bar' => true, 123 => true, 'k' => 'foo', 100 => 'U', 'X' => 'T', 'foo' => 'J'],
						'k2' => [111, 'foo', 'b4r', '100'],
						'k3' => 'unreal',
						'k4' => 'bar2foo',
						'k5' => ['foo', true],
						'k6' => ['x' => 'farm'],
						100 => null
					],
					'C' => [
						5 => false,
						123 => 'foobar',
						'k8' => true,
						'x' => [true, 1, false, 0, null, '2b'],
						'k7' => null,
						1 => ['x' => 'K', 5 => 'U', 'y' => 'M', 3 => null],
						0 => ['a' => 'b22', 'b' => 888]
					],
					667 => 'x1x2',
					'y' => ['k1' => null, 'bar' => 'X'],
					'e' => null,
					'x' => 11111,
					100 => [3 => true, 6 => 'U']
				]
			]
		];
	}
	
	/**
	 * Test <code>intersect</code> method.
	 * 
	 * @testdox Data::intersect($array1, $array2, $depth, $flags) === $expected
	 * @dataProvider provideIntersectData
	 * 
	 * @param array $array1
	 * <p>The method <var>$array1</var> parameter to test with.</p>
	 * @param array $array2
	 * <p>The method <var>$array2</var> parameter to test with.</p>
	 * @param int|null $depth
	 * <p>The method <var>$depth</var> parameter to test with.</p>
	 * @param int $flags
	 * <p>The method <var>$flags</var> parameter to test with.</p>
	 * @param array $expected
	 * <p>The expected method return value.</p>
	 * @return void
	 */
	public function testIntersect(array $array1, array $array2, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::intersect($array1, $array2, $depth, $flags));
	}
	
	/**
	 * Provide <code>intersect</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>intersect</code> method data.</p>
	 */
	public function provideIntersectData(): array
	{
		//initialize
		$array1 = [
			'a' => 123,
			'b' => null,
			'c' => 'bar',
			'd' => [2, 3, 5],
			'y' => ['x', true, 0, 123, false, 'bar', null],
			999 => 'unreal',
			991 => 'foobar',
			'farm' => [
				'potatoes' => 100,
				'broccoli' => 73,
				'cabages' => 200,
				'carrots' => '100'
			],
			'Z' => ['j' => 1],
			'U' => 0,
			'C' => [
				'k1' => ['f', 'j', 'c', 'C', null, 55],
				'k2' => [3, 4, false],
				'k3' => false,
				'k4' => 'foobar',
				'k5' => [],
				'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
				'k7' => [1, 2, 3, 4],
				'k8' => [],
				'k9' => ['y' => 611],
				'k10' => 'i'
			],
			'o' => [
				'bar',
				['a' => 11, 'b' => 4, 'c' => 1],
				[1, 5, 7, 8, 0],
				[false, null, '0'],
				['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
				['foo', 'bar', 'f2b'],
				null,
				false
			]
		];
		$array2 = [
			'b' => 'foo',
			'x' => '123',
			'a' => null,
			999 => 'unreal',
			997 => 'foobar',
			'd' => false,
			'y' => [false, '0', 'x', 123, 'foo', 'bar'],
			'C' => [
				'k11' => ['i' => true, 'j' => false],
				'k1' => [55, 'j', 'c', null, 'x'],
				'k2' => [2, 5, true],
				'k3' => false,
				'k4' => 'f2b',
				'k5' => 'foobar',
				'k6' => ['b' => false, 7 => 'Y', 'y' => 'Z', 'a' => 111, 6 => 'X'],
				'k7' => [4, 3, 2, 1],
				'k8' => ['x' => 33],
				'k9' => 'unreal'
			],
			'o' => [
				'foo',
				['a' => 1, 'b' => 4],
				[4, 5, 6, 8, 7],
				[true, false, null, 0],
				['x' => 1, 5 => 100, 6 => '100', 'y' => 'u'],
				[1 => 'bar', 0 => 'foo', 2 => 'foobar', 3 => 'f2b'],
				true,
				null
			],
			'Z' => 177,
			'farm' => [
				'cabages' => 200,
				'carrots' => 100,
				'potatoes' => 100,
				'unknown' => 0
			],
			'U' => 0
		];
		
		//return
		return [
			[[], [], null, 0x00, []],
			[$array1, $array2, null, 0x00, [
				'y' => ['x', 123, false, 'bar'],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => ['j', 'c', null, 55],
					'k3' => false,
					'k6' => ['a' => 111, 7 => 'Y'],
					'k7' => [1, 2, 3, 4]
				],
				'o' => [
					['b' => 4],
					[5, 7, 8],
					[false, null],
					['x' => 1, 6 => '100'],
					['foo', 'bar'],
					null
				]
			]],
			[$array1, $array2, 0, 0x00, [
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]],
			[$array1, $array2, 1, 0x00, [
				'y' => ['x', 123, false, 'bar'],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => []
				],
				'o' => [
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null
				]
			]],
			[$array1, $array2, null, UData::INTERSECT_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => null,
				'c' => 'bar',
				'd' => [2, 3, 5],
				'y' => ['x', 123, false, 'bar'],
				999 => 'unreal',
				991 => 'foobar',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'Z' => ['j' => 1],
				'U' => 0,
				'C' => [
					'k1' => ['j', 'c', null, 55],
					'k2' => [],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					['a' => 11, 'b' => 4, 'c' => 1],
					[5, 7, 8],
					[false, null],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null
				]
			]],
			[$array1, $array2, 0, UData::INTERSECT_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => null,
				'c' => 'bar',
				'd' => [2, 3, 5],
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				991 => 'foobar',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'Z' => ['j' => 1],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]],
			[$array1, $array2, 1, UData::INTERSECT_ASSOC_EXCLUDE, [
				'a' => 123,
				'b' => null,
				'c' => 'bar',
				'd' => [2, 3, 5],
				'y' => ['x', 123, false, 'bar'],
				999 => 'unreal',
				991 => 'foobar',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'Z' => ['j' => 1],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null
				]
			]],
			[$array1, $array2, null, UData::INTERSECT_NONASSOC_ASSOC, [
				'y' => [3 => 123, 5 => 'bar'],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => [1 => 'j', 2 => 'c'],
					'k3' => false,
					'k6' => ['a' => 111, 7 => 'Y']
				],
				'o' => [
					1 => ['b' => 4],
					2 => [1 => 5, 3 => 8],
					4 => ['x' => 1, 6 => '100'],
					5 => ['foo', 'bar']
				]
			]],
			[$array1, $array2, 0, UData::INTERSECT_NONASSOC_ASSOC, [
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]],
			[$array1, $array2, 1, UData::INTERSECT_NONASSOC_ASSOC, [
				'y' => [3 => 123, 5 => 'bar'],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => []
				],
				'o' => [
					1 => ['a' => 11, 'b' => 4, 'c' => 1],
					2 => [1, 5, 7, 8, 0],
					3 => [false, null, '0'],
					4 => ['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					5 => ['foo', 'bar', 'f2b']
				]
			]],
			[$array1, $array2, null, UData::INTERSECT_NONASSOC_EXCLUDE, [
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k6' => ['a' => 111, 7 => 'Y'],
					'k7' => [1, 2, 3, 4]
				],
				'o' => [
					'bar',
					['b' => 4],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 6 => '100'],
					['foo', 'bar'],
					null,
					false
				]
			]],
			[$array1, $array2, 0, UData::INTERSECT_NONASSOC_EXCLUDE, [
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]],
			[$array1, $array2, 1, UData::INTERSECT_NONASSOC_EXCLUDE, [
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				'farm' => [
					'potatoes' => 100,
					'cabages' => 200
				],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => []
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]],
			[$array1, $array2, null, UData::INTERSECT_ASSOC_EXCLUDE | UData::INTERSECT_NONASSOC_ASSOC, [
				'a' => 123,
				'b' => null,
				'c' => 'bar',
				'd' => [2, 3, 5],
				'y' => [3 => 123, 5 => 'bar'],
				999 => 'unreal',
				991 => 'foobar',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'Z' => ['j' => 1],
				'U' => 0,
				'C' => [
					'k1' => [1 => 'j', 2 => 'c'],
					'k2' => [],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					1 => ['a' => 11, 'b' => 4, 'c' => 1],
					2 => [1 => 5, 3 => 8],
					4 => ['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					5 => ['foo', 'bar', 'f2b']
				]
			]],
			[$array1, $array2, null, UData::INTERSECT_ASSOC_EXCLUDE | UData::INTERSECT_NONASSOC_EXCLUDE, [
				'a' => 123,
				'b' => null,
				'c' => 'bar',
				'd' => [2, 3, 5],
				'y' => ['x', true, 0, 123, false, 'bar', null],
				999 => 'unreal',
				991 => 'foobar',
				'farm' => [
					'potatoes' => 100,
					'broccoli' => 73,
					'cabages' => 200,
					'carrots' => '100'
				],
				'Z' => ['j' => 1],
				'U' => 0,
				'C' => [
					'k1' => ['f', 'j', 'c', 'C', null, 55],
					'k2' => [3, 4, false],
					'k3' => false,
					'k4' => 'foobar',
					'k5' => [],
					'k6' => ['a' => 111, 'b' => true, 5 => 'X', 7 => 'Y', 'x' => 'Z'],
					'k7' => [1, 2, 3, 4],
					'k8' => [],
					'k9' => ['y' => 611],
					'k10' => 'i'
				],
				'o' => [
					'bar',
					['a' => 11, 'b' => 4, 'c' => 1],
					[1, 5, 7, 8, 0],
					[false, null, '0'],
					['x' => 1, 5 => '100', 6 => '100', 'y' => 'U'],
					['foo', 'bar', 'f2b'],
					null,
					false
				]
			]]
		];
	}
}
