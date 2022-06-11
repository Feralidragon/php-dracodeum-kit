<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Managers\Properties\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\meta;
use Dracodeum\Kit\Managers\PropertiesV2\Attributes\Class\propertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property\meta */
class metaTest extends TestCase
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
	 * @param string $meta_name
	 * The meta name to test with.
	 * 
	 * @param mixed $value
	 * The expected value.
	 */
	public function test(string $name, string $meta_name, mixed $value): void
	{
		//initialize
		$manager = new Manager(new metaTest_Class);
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($value, $property->getMetaValue($meta_name));
	}
	
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideData(): array
	{
		return [
			['p1', 'm1', 123],
			['p1', 'm2', '456'],
			['p2', 'm1', 7500],
			['p2', 'm2', '456'],
			['p3', 'm1', 123],
			['p3', 'm2', 'foobar'],
			['p4', 'm1', 1000],
			['p4', 'm2', '777']
		];
	}
}



/** Test case dummy class. */
#[propertyMeta('m1', 'int', '123')]
#[propertyMeta('m2', 'string', 456)]
class metaTest_Class
{
	public $p1;
	
	#[meta('m1', '7500')]
	public $p2;
	
	#[meta('m2', 'foobar')]
	public $p3;
	
	#[meta('m1', '1k'), meta('m2', 777)]
	public $p4;
}
