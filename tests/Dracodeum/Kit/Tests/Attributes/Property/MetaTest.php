<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Meta;
use Dracodeum\Kit\Attributes\Class\PropertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Meta */
class MetaTest extends TestCase
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
		$manager = new Manager(new MetaTest_Class);
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($value, $property->getMetaValue($meta_name));
	}
	
	
	
	//Public static methods
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideData(): array
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
#[PropertyMeta('m1', 'int', '123')]
#[PropertyMeta('m2', 'string', 456)]
class MetaTest_Class
{
	public $p1;
	
	#[Meta('m1', '7500')]
	public $p2;
	
	#[Meta('m2', 'foobar')]
	public $p3;
	
	#[Meta('m1', '1k'), Meta('m2', 777)]
	public $p4;
}
