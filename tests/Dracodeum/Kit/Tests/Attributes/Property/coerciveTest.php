<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\coercive;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @see \Dracodeum\Kit\Attributes\Property\coercive */
class coerciveTest extends TestCase
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
	 * @param bool $typed
	 * Whether or not it is expected to be typed.
	 * 
	 * @param bool $nullable
	 * Whether or not it is expected to be nullable.
	 * 
	 * @return void
	 */
	public function testProperties(string $name, bool $typed, bool $nullable = false): void
	{
		//initialize
		$manager = new Manager(new coerciveTest_Class());
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($typed, $property->hasType());
		if ($typed) {
			$this->assertFalse($property->getType()->strict);
			$this->assertSame($nullable, $property->getType()->nullable);
		}
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
class coerciveTest_Class
{
	public $p1;
	
	#[coercive]
	public $p2;
	
	#[coercive]
	public int $p3 = 0;
	
	#[coercive]
	public ?int $p4 = 0;
	
	#[coercive]
	public int|string|null $p5 = 0;
	
	#[coercive('int')]
	public $p6;
	
	#[coercive('?int')]
	public $p7;
}
