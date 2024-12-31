<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Attributes\Property;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Attributes\Property\Coercive;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

/** @covers \Dracodeum\Kit\Attributes\Property\Coercive */
class CoerciveTest extends TestCase
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
	 * @param bool $typed
	 * Whether it is expected to be typed.
	 * 
	 * @param bool $nullable
	 * Whether it is expected to be nullable.
	 */
	public function test(string $name, bool $typed, bool $nullable = false): void
	{
		//initialize
		$manager = new Manager(new CoerciveTest_Class);
		$property = $manager->getProperty($name);
		
		//assert
		$this->assertSame($typed, $property->hasType());
		if ($typed) {
			$this->assertFalse($property->getType()->strict);
			$this->assertSame($nullable, $property->getType()->nullable);
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
class CoerciveTest_Class
{
	public $p1;
	
	#[Coercive]
	public $p2;
	
	#[Coercive]
	public int $p3 = 0;
	
	#[Coercive]
	public ?int $p4 = 0;
	
	#[Coercive]
	public int|string|null $p5 = 0;
	
	#[Coercive('int')]
	public $p6;
	
	#[Coercive('?int')]
	public $p7;
}
