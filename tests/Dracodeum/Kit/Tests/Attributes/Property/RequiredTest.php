<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Required;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Required */
class RequiredTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 */
	public function test(): void
	{
		//initialize
		$manager = new Manager(new RequiredTest_Class);
		
		//assert (p1)
		$this->assertFalse($manager->getProperty('p1')->isRequired());
		
		//assert (p2)
		$this->assertTrue($manager->getProperty('p2')->isRequired());
	}
}



/** Test case dummy class. */
class RequiredTest_Class
{
	public $p1;
	
	#[Required]
	public $p2;
}
