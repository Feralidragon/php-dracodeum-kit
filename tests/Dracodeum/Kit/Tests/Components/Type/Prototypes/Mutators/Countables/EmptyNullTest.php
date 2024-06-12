<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Countables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\EmptyNull as Prototype;
use Countable as ICountable;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\EmptyNull */
class EmptyNullTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected value.
	 */
	public function testProcess(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
	}
	
	
	
	//Public static methods
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData(): array
	{
		//initialize
		$c1 = new EmptyNullTest_Class1;
		$c2 = new EmptyNullTest_Class2;
		
		//return
		return [
			[[], null],
			[$c1, null],
			[[[]], [[]]],
			[[1, 2, 3], [1, 2, 3]],
			[['foobar'], ['foobar']],
			[$c2, $c2]
		];
	}
}



/** Test case dummy class 1. */
class EmptyNullTest_Class1 implements ICountable
{
	public function count(): int
	{
		return 0;
	}
}



/** Test case dummy class 2. */
class EmptyNullTest_Class2 implements ICountable
{
	public function count(): int
	{
		return 5;
	}
}
