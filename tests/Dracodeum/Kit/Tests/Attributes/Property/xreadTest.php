<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\xread;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\xread */
class xreadTest extends TestCase
{
	//Public methods
	/**
	 * Test property.
	 * 
	 * @dataProvider providePropertyData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $mode
	 * The expected mode.
	 * 
	 * @param bool $affect_subclasses
	 * Whether or not it is expected to affect subclasses.
	 * 
	 * @return void
	 */
	public function testProperty(string $name, string $mode, bool $affect_subclasses = false): void
	{
		//initialize
		$manager = new Manager(new xreadTest_Class());
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($mode, $property->getMode());
		$this->assertSame($affect_subclasses, $property->areSubclassesAffectedByMode());
	}
	
	/**
	 * Provide property data.
	 * 
	 * @return array
	 * The data.
	 */
	public function providePropertyData(): array
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
