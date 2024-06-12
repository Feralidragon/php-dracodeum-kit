<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\EmptyNull as Prototype;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\EmptyNull */
class EmptyNullTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected value.
	 */
	public function testProcess(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
	}
	
	
	
	//Public static methods
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData(): array
	{
		//initialize
		$c1 = new EmptyNullTest_Class('');
		$c2 = new EmptyNullTest_Class(' ');
		$c3 = new EmptyNullTest_Class('a');
		
		//return
		return [
			['', null],
			[' ', ' '],
			['a', 'a'],
			[$c1, null],
			[$c2, $c2],
			[$c3, $c3]
		];
	}
}



/** Test case dummy class. */
class EmptyNullTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
