<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Numericals;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Multiples as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Multiples */
class MultiplesTest extends TestCase
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
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess(mixed $value, array $properties): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($v, $value);
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Test `ExplanationProducer` interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [[1]])->getExplanation());
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
		return [
			[0, [[2]]],
			[2, [[2]]],
			[4, [[2]]],
			[-2, [[2]]],
			[-4, [[2]]],
			[0, [[2, 5, 7]]],
			[2, [[2, 5, 7]]],
			[5, [[2, 5, 7]]],
			[7, [[2, 5, 7]]],
			[12, [[2, 5, 7]]],
			[15, [[2, 5, 7]]],
			[21, [[2, 5, 7]]],
			[35, [[2, 5, 7]]],
			[-2, [[2, 5, 7]]],
			[-5, [[2, 5, 7]]],
			[-7, [[2, 5, 7]]],
			[-12, [[2, 5, 7]]],
			[-15, [[2, 5, 7]]],
			[-21, [[2, 5, 7]]],
			[-35, [[2, 5, 7]]],
			[0, [[1.5, 2, 2.25, 7]]],
			[1.5, [[1.5, 2, 2.25, 7]]],
			[2, [[1.5, 2, 2.25, 7]]],
			[2.25, [[1.5, 2, 2.25, 7]]],
			[3, [[1.5, 2, 2.25, 7]]],
			[4, [[1.5, 2, 2.25, 7]]],
			[4.5, [[1.5, 2, 2.25, 7]]],
			[7.0, [[1.5, 2, 2.25, 7]]],
			[11.25, [[1.5, 2, 2.25, 7]]],
			[14.0, [[1.5, 2, 2.25, 7]]],
			[21.0, [[1.5, 2, 2.25, 7]]],
			[-1.5, [[1.5, 2, 2.25, 7]]],
			[-2, [[1.5, 2, 2.25, 7]]],
			[-2.25, [[1.5, 2, 2.25, 7]]],
			[-3, [[1.5, 2, 2.25, 7]]],
			[-4, [[1.5, 2, 2.25, 7]]],
			[-4.5, [[1.5, 2, 2.25, 7]]],
			[-7.0, [[1.5, 2, 2.25, 7]]],
			[-11.25, [[1.5, 2, 2.25, 7]]],
			[-14.0, [[1.5, 2, 2.25, 7]]],
			[-21.0, [[1.5, 2, 2.25, 7]]],
			[1, [[2], 'negate' => true]],
			[3, [[2], 'negate' => true]],
			[-1, [[2], 'negate' => true]],
			[-3, [[2], 'negate' => true]],
			[1, [[2, 5, 7], 'negate' => true]],
			[3, [[2, 5, 7], 'negate' => true]],
			[11, [[2, 5, 7], 'negate' => true]],
			[33, [[2, 5, 7], 'negate' => true]],
			[-1, [[2, 5, 7], 'negate' => true]],
			[-3, [[2, 5, 7], 'negate' => true]],
			[-11, [[2, 5, 7], 'negate' => true]],
			[-33, [[2, 5, 7], 'negate' => true]],
			[1, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[1.6, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[2.251, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[11.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-1, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-1.6, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-2.251, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-11.0, [[1.5, 2, 2.25, 7], 'negate' => true]]
		];
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error(): array
	{
		return [
			[0, [[2], 'negate' => true]],
			[2, [[2], 'negate' => true]],
			[4, [[2], 'negate' => true]],
			[-2, [[2], 'negate' => true]],
			[-4, [[2], 'negate' => true]],
			[0, [[2, 5, 7], 'negate' => true]],
			[2, [[2, 5, 7], 'negate' => true]],
			[5, [[2, 5, 7], 'negate' => true]],
			[7, [[2, 5, 7], 'negate' => true]],
			[12, [[2, 5, 7], 'negate' => true]],
			[15, [[2, 5, 7], 'negate' => true]],
			[21, [[2, 5, 7], 'negate' => true]],
			[35, [[2, 5, 7], 'negate' => true]],
			[-2, [[2, 5, 7], 'negate' => true]],
			[-5, [[2, 5, 7], 'negate' => true]],
			[-7, [[2, 5, 7], 'negate' => true]],
			[-12, [[2, 5, 7], 'negate' => true]],
			[-15, [[2, 5, 7], 'negate' => true]],
			[-21, [[2, 5, 7], 'negate' => true]],
			[-35, [[2, 5, 7], 'negate' => true]],
			[0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[1.5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[2, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[2.25, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[3, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[4, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[4.5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[7.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[11.25, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[14.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[21.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-1.5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-2, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-2.25, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-3, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-4, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-4.5, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-7.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-11.25, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-14.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[-21.0, [[1.5, 2, 2.25, 7], 'negate' => true]],
			[1, [[2]]],
			[3, [[2]]],
			[-1, [[2]]],
			[-3, [[2]]],
			[1, [[2, 5, 7]]],
			[3, [[2, 5, 7]]],
			[11, [[2, 5, 7]]],
			[33, [[2, 5, 7]]],
			[-1, [[2, 5, 7]]],
			[-3, [[2, 5, 7]]],
			[-11, [[2, 5, 7]]],
			[-33, [[2, 5, 7]]],
			[1, [[1.5, 2, 2.25, 7]]],
			[1.6, [[1.5, 2, 2.25, 7]]],
			[2.251, [[1.5, 2, 2.25, 7]]],
			[5, [[1.5, 2, 2.25, 7]]],
			[11.0, [[1.5, 2, 2.25, 7]]],
			[-1, [[1.5, 2, 2.25, 7]]],
			[-1.6, [[1.5, 2, 2.25, 7]]],
			[-2.251, [[1.5, 2, 2.25, 7]]],
			[-5, [[1.5, 2, 2.25, 7]]],
			[-11.0, [[1.5, 2, 2.25, 7]]]
		];
	}
}
