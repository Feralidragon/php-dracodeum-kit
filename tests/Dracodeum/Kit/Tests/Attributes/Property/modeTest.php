<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\mode;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\mode */
class modeTest extends TestCase
{
	//Public methods
	/**
	 * Test properties.
	 * 
	 * @dataProvider providePropertiesData
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
	public function testProperties(string $name, string $mode, bool $affect_subclasses = false): void
	{
		//initialize
		$manager = new Manager(new modeTest_Class());
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($mode, $property->getMode());
		$this->assertSame($affect_subclasses, $property->areSubclassesAffectedByMode());
	}
	
	/**
	 * Provide properties data.
	 * 
	 * @return array
	 * The data.
	 */
	public function providePropertiesData(): array
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
