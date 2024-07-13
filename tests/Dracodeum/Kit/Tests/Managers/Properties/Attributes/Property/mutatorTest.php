<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\{
	coercive,
	mutator
};
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;

/** @covers \Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\mutator */
class mutatorTest extends TestCase
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
		$manager = new Manager(new mutatorTest_Class);
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
class mutatorTest_Class
{
	#[mutator('non_empty')]
	public string $p1 = '...';
	
	#[mutator(mutatorTest_Prototype::class)]
	public string $p2 = '...';
	
	#[coercive]
	public string $p3 = '...';
	
	#[coercive, mutator('non_empty')]
	public string $p4 = '...';
	
	#[coercive, mutator(mutatorTest_Prototype::class)]
	public string $p5 = '...';
	
	#[mutator('non_empty'), coercive]
	public string $p6 = '...';
	
	#[mutator(mutatorTest_Prototype::class), coercive]
	public string $p7 = '...';
}



/** Test case prototype class. */
class mutatorTest_Prototype extends Prototype
{
	public function process(mixed &$value) {}
}
