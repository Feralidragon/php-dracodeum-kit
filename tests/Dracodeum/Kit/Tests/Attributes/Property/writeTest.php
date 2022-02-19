<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\write;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\write */
class writeTest extends TestCase
{
	//Public methods
	/**
	 * Test properties.
	 * 
	 * @dataProvider providePropertiesData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $mode
	 * The expected mode.
	 * 
	 * @param bool $affect_subclasses
	 * Whether or not it is expected to affect subclasses.
	 * 
	 * @return void
	 */
	public function testProperties(string $name, string $mode, bool $affect_subclasses = false): void
	{
		//initialize
		$manager = new Manager(new writeTest_Class());
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($mode, $property->getMode());
		$this->assertSame($affect_subclasses, $property->areSubclassesAffectedByMode());
	}
	
	/**
	 * Provide properties data.
	 * 
	 * @return array
	 * The data.
	 */
	public function providePropertiesData(): array
	{
		return [
			['p1', 'rw'],
			['p2', 'w'],
			['p3', 'w', true]
		];
	}
}



/** Test case dummy class. */
class writeTest_Class
{
	public $p1;
	
	#[write]
	public $p2;
	
	#[write(true)]
	public $p3;
}
