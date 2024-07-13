<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Interfaces\Attribute\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Class\propertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyPostInitializer as IPropertyPostInitializer;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/** @covers \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyPostInitializer */
class PropertyPostInitializerTest extends TestCase
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
		$manager = new Manager(new PropertyPostInitializerTest_Class);
		
		//assert
		$this->assertSame(0, $manager->getProperty('p1')->getMetaValue('m1'));
		$this->assertSame(123, $manager->getProperty('p2')->getMetaValue('m1'));
	}
}



/** Test case attribute class. */
#[Attribute(Attribute::TARGET_PROPERTY)]
class PropertyPostInitializerTest_Attribute implements IPropertyPostInitializer
{
	public function postInitializeProperty(Property $property): void
	{
		$property->setMetaValue('m1', '123');
	}
}



/** Test case dummy class. */
#[propertyMeta('m1', 'int', 0)]
class PropertyPostInitializerTest_Class
{
	public $p1;
	
	#[PropertyPostInitializerTest_Attribute]
	public $p2;
}
