<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Class;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Class\propertyMeta;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;
use Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type as ENumberType;

/** @see \Dracodeum\Kit\Attributes\Class\propertyMeta */
class propertyMetaTest extends TestCase
{
	//Public methods
	/**
	 * Test meta.
	 * 
	 * @dataProvider provideMetaData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param bool $exists
	 * Whether or not it is expected to exist.
	 * 
	 * @param mixed $default
	 * The expected default.
	 * 
	 * @param bool $nullable
	 * Whether or not it is expected to be nullable.
	 * 
	 * @return void
	 */
	public function testMeta(string $name, bool $exists, mixed $default = null, bool $nullable = false): void
	{
		//initialize
		$manager = new Manager(new propertyMetaTest_Class());
		$meta = $manager->getMeta();
		
		//assert
		$this->assertSame($exists, $meta->has($name));
		if ($exists) {
			$entry = $meta->get($name);
			$this->assertSame($default, $entry->default);
			$this->assertSame($nullable, $entry->type->nullable);
		}
	}
	
	/**
	 * Provide meta data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMetaData(): array
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
#[propertyMeta('m1', 'int', '123')]
#[propertyMeta('m2', 'string', 456)]
#[propertyMeta('m3', '?number', 789, type: ENumberType::FLOAT)]
class propertyMetaTest_Class {}
