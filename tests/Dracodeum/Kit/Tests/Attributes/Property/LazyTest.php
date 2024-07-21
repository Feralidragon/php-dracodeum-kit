<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Lazy;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Lazy */
class LazyTest extends TestCase
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
		$manager = new Manager(new LazyTest_Class);
		
		//assert (p1)
		$this->assertFalse($manager->getProperty('p1')->isLazy());
		
		//assert (p2)
		$this->assertTrue($manager->getProperty('p2')->isLazy());
	}
}



/** Test case dummy class. */
class LazyTest_Class
{
	public $p1;
	
	#[Lazy]
	public $p2;
}
