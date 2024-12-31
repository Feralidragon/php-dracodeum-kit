<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Interfaces\Attribute\Class;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class\MetaInitializer as IMetaInitializer;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Managers\PropertiesV2\Meta;
use Dracodeum\Kit\Components\Type;
use Attribute;

/** @covers \Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Class\MetaInitializer */
class MetaInitializerTest extends TestCase
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
		$meta = (new Manager(new MetaInitializerTest_Class))->getMeta();
		
		//assert
		$this->assertFalse($meta->has('m0'));
		$this->assertTrue($meta->has('m1'));
		$this->assertSame(123, $meta->get('m1')->default);
	}
}



/** Test case attribute class. */
#[Attribute(Attribute::TARGET_CLASS)]
class MetaInitializerTest_Attribute implements IMetaInitializer
{
	public function initializeMeta(Meta $meta): void
	{
		$meta->set('m1', Type::build('int'), '123');
	}
}



/** Test case dummy class. */
#[MetaInitializerTest_Attribute]
class MetaInitializerTest_Class {}
