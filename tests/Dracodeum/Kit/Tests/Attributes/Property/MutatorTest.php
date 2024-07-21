<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\{
	Coercive,
	Mutator
};
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;

/** @covers \Dracodeum\Kit\Attributes\Property\Mutator */
class MutatorTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * @dataProvider provideData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param bool $typed
	 * Whether or not it is expected to be typed.
	 * 
	 * @param bool $mutated
	 * Whether or not it is expected to be mutated.
	 */
	public function test(string $name, bool $typed, bool $mutated): void
	{
		//initialize
		$manager = new Manager(new MutatorTest_Class);
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($typed, $property->hasType());
		if ($typed) {
			$this->assertSame($mutated, $property->getType()->hasMutators());
		}
	}
	
	
	
	//Public static methods
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideData(): array
	{
		return [
			['p1', false, false],
			['p2', false, false],
			['p3', true, false],
			['p4', true, true],
			['p5', true, true],
			['p6', true, false],
			['p7', true, false]
		];
	}
}



/** Test case dummy class. */
class MutatorTest_Class
{
	#[Mutator('non_empty')]
	public string $p1 = '...';
	
	#[Mutator(mutatorTest_Prototype::class)]
	public string $p2 = '...';
	
	#[Coercive]
	public string $p3 = '...';
	
	#[Coercive, Mutator('non_empty')]
	public string $p4 = '...';
	
	#[Coercive, Mutator(mutatorTest_Prototype::class)]
	public string $p5 = '...';
	
	#[Mutator('non_empty'), Coercive]
	public string $p6 = '...';
	
	#[Mutator(MutatorTest_Prototype::class), Coercive]
	public string $p7 = '...';
}



/** Test case prototype class. */
class MutatorTest_Prototype extends Prototype
{
	public function process(mixed &$value) {}
}
