<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\ignore;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\ignore */
class ignoreTest extends TestCase
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
		$manager = new Manager(new ignoreTest_Class());
		
		//assert (p1)
		$this->assertTrue($manager->hasProperty('p1'));
		$this->assertFalse($manager->getProperty('p1')->isIgnored());
		
		//assert (p2)
		$this->assertFalse($manager->hasProperty('p2'));
	}
}



/** Test case dummy class. */
class ignoreTest_Class
{
	public $p1;
	
	#[ignore]
	public $p2;
}
