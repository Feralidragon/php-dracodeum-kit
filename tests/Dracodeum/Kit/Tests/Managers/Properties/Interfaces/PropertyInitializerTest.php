<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Interfaces;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Class\PropertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;

/** @covers \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\PropertyInitializer */
class PropertyInitializerTest extends TestCase
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
		$manager = new Manager(new PropertyInitializerTest_Class);
		
		//assert
		$this->assertSame(0, $manager->getProperty('p1')->getMetaValue('m1'));
		$this->assertSame(123, $manager->getProperty('p2')->getMetaValue('m1'));
	}
}



/** Test case dummy class. */
#[PropertyMeta('m1', 'int', 0)]
class PropertyInitializerTest_Class implements IPropertyInitializer
{
	public $p1;
	
	public $p2;
	
	public static function initializeProperty(Property $property): void
	{
		if ($property->getName() === 'p2') {
			$property->setMetaValue('m1', '123');
		}
	}
}
