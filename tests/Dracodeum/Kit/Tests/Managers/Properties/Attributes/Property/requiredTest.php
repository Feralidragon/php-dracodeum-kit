<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\required;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\required */
class requiredTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * 
	 * @return void
	 */
	public function test(): void
	{
		//initialize
		$manager = new Manager(new requiredTest_Class());
		
		//assert (p1)
		$this->assertFalse($manager->getProperty('p1')->isRequired());
		
		//assert (p2)
		$this->assertTrue($manager->getProperty('p2')->isRequired());
	}
}



/** Test case dummy class. */
class requiredTest_Class
{
	public $p1;
	
	#[required]
	public $p2;
}
