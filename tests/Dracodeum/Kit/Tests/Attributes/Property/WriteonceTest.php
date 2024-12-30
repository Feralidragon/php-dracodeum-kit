<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Writeonce;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Writeonce */
class WriteonceTest extends TestCase
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
	 * @param string $mode
	 * The expected mode.
	 * 
	 * @param bool $affect_subclasses
	 * Whether it is expected to affect subclasses.
	 */
	public function test(string $name, string $mode, bool $affect_subclasses = false): void
	{
		//initialize
		$manager = new Manager(new WriteonceTest_Class);
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($mode, $property->getMode());
		$this->assertSame($affect_subclasses, $property->areSubclassesAffectedByMode());
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
			['p1', 'rw'],
			['p2', 'w-'],
			['p3', 'w-', true]
		];
	}
}



/** Test case dummy class. */
class WriteonceTest_Class
{
	public $p1;
	
	#[Writeonce]
	public $p2;
	
	#[Writeonce(true)]
	public $p3;
}
