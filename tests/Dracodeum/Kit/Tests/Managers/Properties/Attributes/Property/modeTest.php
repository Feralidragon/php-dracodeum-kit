<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\mode;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\mode */
class modeTest extends TestCase
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
		$manager = new Manager(new modeTest_Class);
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
			['p3', 'r', true],
			['p4', 'r+'],
			['p5', 'r+', true],
			['p6', 'rw'],
			['p7', 'rw', true],
			['p8', 'w'],
			['p9', 'w', true],
			['p10', 'w-'],
			['p11', 'w-', true]
		];
	}
}



/** Test case dummy class. */
class modeTest_Class
{
	public $p1;
	
	#[mode('r')]
	public $p2;
	
	#[mode('r', true)]
	public $p3;
	
	#[mode('r+')]
	public $p4;
	
	#[mode('r+', true)]
	public $p5;
	
	#[mode('rw')]
	public $p6;
	
	#[mode('rw', true)]
	public $p7;
	
	#[mode('w')]
	public $p8;
	
	#[mode('w', true)]
	public $p9;
	
	#[mode('w-')]
	public $p10;
	
	#[mode('w-', true)]
	public $p11;
}
