<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\xread;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\xread */
class xreadTest extends TestCase
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
	 * Whether or not it is expected to affect subclasses.
	 */
	public function test(string $name, string $mode, bool $affect_subclasses = false): void
	{
		//initialize
		$manager = new Manager(new xreadTest_Class);
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
			['p2', 'r'],
			['p3', 'r', true]
		];
	}
}



/** Test case dummy class. */
class xreadTest_Class
{
	public $p1;
	
	#[xread]
	public $p2;
	
	#[xread(true)]
	public $p3;
}
