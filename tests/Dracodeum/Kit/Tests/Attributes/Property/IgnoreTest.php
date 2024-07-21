<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Ignore;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Ignore */
class IgnoreTest extends TestCase
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
		$manager = new Manager(new IgnoreTest_Class);
		
		//assert (p1)
		$this->assertTrue($manager->hasProperty('p1'));
		$this->assertFalse($manager->getProperty('p1')->isIgnored());
		
		//assert (p2)
		$this->assertFalse($manager->hasProperty('p2'));
	}
}



/** Test case dummy class. */
class IgnoreTest_Class
{
	public $p1;
	
	#[Ignore]
	public $p2;
}
