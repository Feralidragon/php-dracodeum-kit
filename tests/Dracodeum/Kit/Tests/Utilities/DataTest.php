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
	
	/**
	 * Test <code>merge</code> method.
	 * 
	 * @dataProvider provideMergeMethodData
	 * @testdox Data::merge($array1, $array2, $depth, $flags) === $expected
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
	public function testMergeMethod(array $array1, array $array2, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::merge($array1, $array2, $depth, $flags));
	}
	
	/**
	 * Provide <code>merge</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>merge</code> method data.</p>
	 */
	public function provideMergeMethodData(): array
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
				'k4' => [2 => 3, 3 => 3, 'k' => '#']
			],
			'farm' => [
				'carrots' => 100,
				'potatoes' => 2412,
				'cabages' => 'unknown'
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
			'zed' => [
				'k1' => ['Y', 'F', 'X', 'Z', 'X', 'K'],
				'k2' => 'foo2bar',
				'k3' => true,
				'k4' => [3, false, null]
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [3, false, null]
				],
				'farm' => [
					'broccoli' => 73,
					'potatoes' => 1678,
					'carrots' => 125
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k3' => true
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown',
					'broccoli' => 73
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
					'k4' => [2 => null, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 125,
					'potatoes' => 1678,
					'cabages' => 'unknown'
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
					'k4' => [2 => 3, 3 => 3, 'k' => '#']
				],
				'farm' => [
					'carrots' => 100,
					'potatoes' => 2412,
					'cabages' => 'unknown'
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
						'k4' => [2 => 3, 3 => 3, 'k' => '#']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
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
						'k3' => true
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown',
						'broccoli' => 73
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
						'k4' => [2 => 3, 3 => 3, 'k' => '#']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
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
						'k4' => [2 => null, 3 => 3, 'k' => '#']
					],
					'farm' => [
						'carrots' => 125,
						'potatoes' => 1678,
						'cabages' => 'unknown'
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
						'k4' => [2 => 3, 3 => 3, 'k' => '#']
					],
					'farm' => [
						'carrots' => 100,
						'potatoes' => 2412,
						'cabages' => 'unknown'
					]
				]
			]
		];
	}
	
	/**
	 * Test <code>unique</code> method.
	 * 
	 * @dataProvider provideUniqueMethodData
	 * @testdox Data::unique($array, $depth, $flags) === $expected
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
	public function testUniqueMethod(array $array, ?int $depth, int $flags, array $expected): void
	{
		$this->assertSame($expected, UData::unique($array, $depth, $flags));
	}
	
	/**
	 * Provide <code>unique</code> method data.
	 * 
	 * @return array
	 * <p>The provided <code>unique</code> method data.</p>
	 */
	public function provideUniqueMethodData(): array
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
			'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'z' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
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
				'e' => ['x' => 333, 'y' => 'u']
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
				'd' => ['x' => 333, 'y' => 'u']
			]]
		];
	}
}
