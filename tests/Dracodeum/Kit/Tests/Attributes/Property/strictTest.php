<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\strict;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\strict */
class strictTest extends TestCase
{
	//Public methods
	/**
	 * Test property.
	 * 
	 * @dataProvider providePropertyData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param bool $typed
	 * Whether or not it is expected to be typed.
	 * 
	 * @param bool $nullable
	 * Whether or not it is expected to be nullable.
	 * 
	 * @return void
	 */
	public function testProperty(string $name, bool $typed, bool $nullable = false): void
	{
		//initialize
		$manager = new Manager(new strictTest_Class());
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($typed, $property->hasType());
		if ($typed) {
			$this->assertTrue($property->getType()->strict);
			$this->assertSame($nullable, $property->getType()->nullable);
		}
	}
	
	/**
	 * Provide property data.
	 * 
	 * @return array
	 * The data.
	 */
	public function providePropertyData(): array
	{
		return [
			['p1', false],
			['p2', false],
			['p3', true],
			['p4', true, true],
			['p5', true, true],
			['p6', true],
			['p7', true, true]
		];
	}
}



/** Test case dummy class. */
class strictTest_Class
{
	public $p1;
	
	#[strict]
	public $p2;
	
	#[strict]
	public int $p3 = 0;
	
	#[strict]
	public ?int $p4 = 0;
	
	#[strict]
	public int|string|null $p5 = 0;
	
	#[strict('int')]
	public $p6;
	
	#[strict('?int')]
	public $p7;
}
