<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Class;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Class\PropertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type as ENumberType;

/** @covers \Dracodeum\Kit\Attributes\Class\PropertyMeta */
class PropertyMetaTest extends TestCase
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
	 * @param bool $exists
	 * Whether it is expected to exist.
	 * 
	 * @param mixed $default
	 * The expected default.
	 * 
	 * @param bool $nullable
	 * Whether it is expected to be nullable.
	 */
	public function test(string $name, bool $exists, mixed $default = null, bool $nullable = false): void
	{
		//initialize
		$manager = new Manager(new PropertyMetaTest_Class);
		$meta = $manager->getMeta();
		
		//assert
		$this->assertSame($exists, $meta->has($name));
		if ($exists) {
			$entry = $meta->get($name);
			$this->assertSame($default, $entry->default);
			$this->assertSame($nullable, $entry->type->nullable);
		}
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
			['m0', false],
			['m1', true, 123],
			['m2', true, '456'],
			['m3', true, 789.0, true]
		];
	}
}



/** Test case dummy class. */
#[PropertyMeta('m1', 'int', '123')]
#[PropertyMeta('m2', 'string', 456)]
#[PropertyMeta('m3', '?number', 789, type: ENumberType::FLOAT)]
class PropertyMetaTest_Class {}
