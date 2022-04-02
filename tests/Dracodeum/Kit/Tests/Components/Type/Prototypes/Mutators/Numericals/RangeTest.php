<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Numericals;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Range as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Range */
class RangeTest extends TestCase
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
	 * 
	 * @return void
	 */
	public function testProcess(mixed $value, array $properties): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($v, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		return [
			[0, [0, 0]],
			[0, [0, 2]],
			[1, [0, 2]],
			[2, [0, 2]],
			[0.5, [0.5, 1.5]],
			[1, [0.5, 1.5]],
			[1.5, [0.5, 1.5]],
			[-2, [-2, -0.5]],
			[-1, [-2, -0.5]],
			[-0.5, [-2, -0.5]],
			[75, [75, 123]],
			[100, [75, 123]],
			[123, [75, 123]],
			[-204.375, [-204.375, -123.456]],
			[-204.1, [-204.375, -123.456]],
			[-150.777, [-204.375, -123.456]],
			[-123.9, [-204.375, -123.456]],
			[-123.456, [-204.375, -123.456]],
			[1, [0, 2, 'min_exclusive' => true]],
			[2, [0, 2, 'min_exclusive' => true]],
			[1, [0.5, 1.5, 'min_exclusive' => true]],
			[1.5, [0.5, 1.5, 'min_exclusive' => true]],
			[-1, [-2, -0.5], 'min_exclusive' => true],
			[-0.5, [-2, -0.5], 'min_exclusive' => true],
			[100, [75, 123], 'min_exclusive' => true],
			[123, [75, 123], 'min_exclusive' => true],
			[-204.1, [-204.375, -123.456], 'min_exclusive' => true],
			[-150.777, [-204.375, -123.456], 'min_exclusive' => true],
			[-123.9, [-204.375, -123.456], 'min_exclusive' => true],
			[-123.456, [-204.375, -123.456], 'min_exclusive' => true],
			[0, [0, 2], 'max_exclusive' => true],
			[1, [0, 2], 'max_exclusive' => true],
			[0.5, [0.5, 1.5], 'max_exclusive' => true],
			[1, [0.5, 1.5], 'max_exclusive' => true],
			[-2, [-2, -0.5], 'max_exclusive' => true],
			[-1, [-2, -0.5], 'max_exclusive' => true],
			[75, [75, 123], 'max_exclusive' => true],
			[100, [75, 123], 'max_exclusive' => true],
			[-204.375, [-204.375, -123.456, 'max_exclusive' => true]],
			[-204.1, [-204.375, -123.456, 'max_exclusive' => true]],
			[-150.777, [-204.375, -123.456, 'max_exclusive' => true]],
			[-123.9, [-204.375, -123.456, 'max_exclusive' => true]],
			[1, [0, 2, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1, [0.5, 1.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-1, [-2, -0.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[100, [75, 123], 'min_exclusive' => true, 'max_exclusive' => true],
			[-204.1, [-204.375, -123.456], 'min_exclusive' => true, 'max_exclusive' => true],
			[-150.777, [-204.375, -123.456], 'min_exclusive' => true, 'max_exclusive' => true],
			[-123.9, [-204.375, -123.456], 'min_exclusive' => true, 'max_exclusive' => true],
			[-1, [0, 0, 'negate' => true]],
			[1, [0, 0, 'negate' => true]],
			[-1, [0, 2, 'negate' => true]],
			[3, [0, 2, 'negate' => true]],
			[0.25, [0.5, 1.5, 'negate' => true]],
			[1.75, [0.5, 1.5, 'negate' => true]],
			[-2.5, [-2, -0.5, 'negate' => true]],
			[-0.3, [-2, -0.5, 'negate' => true]],
			[74, [75, 123, 'negate' => true]],
			[124, [75, 123, 'negate' => true]],
			[-204.4, [-204.375, -123.456, 'negate' => true]],
			[-123.4, [-204.375, -123.456, 'negate' => true]],
			[-1, [0, 0, 'negate' => true, 'min_exclusive' => true]],
			[0, [0, 0, 'negate' => true, 'min_exclusive' => true]],
			[1, [0, 0, 'negate' => true, 'min_exclusive' => true]],
			[-1, [0, 2, 'negate' => true, 'min_exclusive' => true]],
			[0, [0, 2, 'negate' => true, 'min_exclusive' => true]],
			[3, [0, 2, 'negate' => true, 'min_exclusive' => true]],
			[0.25, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true]],
			[0.5, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true]],
			[1.75, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true]],
			[-2.5, [-2, -0.5, 'negate' => true, 'min_exclusive' => true]],
			[-2, [-2, -0.5, 'negate' => true, 'min_exclusive' => true]],
			[-0.3, [-2, -0.5, 'negate' => true, 'min_exclusive' => true]],
			[74, [75, 123, 'negate' => true, 'min_exclusive' => true]],
			[75, [75, 123, 'negate' => true, 'min_exclusive' => true]],
			[124, [75, 123, 'negate' => true, 'min_exclusive' => true]],
			[-204.4, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-204.375, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-123.4, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-1, [0, 0, 'negate' => true, 'max_exclusive' => true]],
			[0, [0, 0, 'negate' => true, 'max_exclusive' => true]],
			[1, [0, 0, 'negate' => true, 'max_exclusive' => true]],
			[-1, [0, 2, 'negate' => true, 'max_exclusive' => true]],
			[2, [0, 2, 'negate' => true, 'max_exclusive' => true]],
			[3, [0, 2, 'negate' => true, 'max_exclusive' => true]],
			[0.25, [0.5, 1.5, 'negate' => true, 'max_exclusive' => true]],
			[1.5, [0.5, 1.5, 'negate' => true, 'max_exclusive' => true]],
			[1.75, [0.5, 1.5, 'negate' => true, 'max_exclusive' => true]],
			[-2.5, [-2, -0.5, 'negate' => true, 'max_exclusive' => true]],
			[-0.5, [-2, -0.5, 'negate' => true, 'max_exclusive' => true]],
			[-0.3, [-2, -0.5, 'negate' => true, 'max_exclusive' => true]],
			[74, [75, 123, 'negate' => true, 'max_exclusive' => true]],
			[123, [75, 123, 'negate' => true, 'max_exclusive' => true]],
			[124, [75, 123, 'negate' => true, 'max_exclusive' => true]],
			[-204.4, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-123.456, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-123.4, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-1, [0, 0, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0, [0, 0, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1, [0, 0, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-1, [0, 2, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0, [0, 2, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[2, [0, 2, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[3, [0, 2, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0.25, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0.5, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1.5, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1.75, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-2.5, [-2, -0.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-2, [-2, -0.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-0.5, [-2, -0.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-0.3, [-2, -0.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[74, [75, 123, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[75, [75, 123, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[123, [75, 123, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[124, [75, 123, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-204.4, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-204.375, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-123.456, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-123.4, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]]
		];
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
	 * 
	 * @return void
	 */
	public function testProcess_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		return [
			[-1, [0, 0]],
			[1, [0, 0]],
			[-1, [0, 2]],
			[3, [0, 2]],
			[0.25, [0.5, 1.5]],
			[1.75, [0.5, 1.5]],
			[-2.5, [-2, -0.5]],
			[-0.3, [-2, -0.5]],
			[74, [75, 123]],
			[124, [75, 123]],
			[-204.9, [-204.375, -123.456]],
			[-123.1, [-204.375, -123.456]],
			[-1, [0, 0, 'min_exclusive' => true]],
			[0, [0, 0, 'min_exclusive' => true]],
			[1, [0, 0, 'min_exclusive' => true]],
			[-1, [0, 2, 'min_exclusive' => true]],
			[0, [0, 2, 'min_exclusive' => true]],
			[3, [0, 2, 'min_exclusive' => true]],
			[0.25, [0.5, 1.5, 'min_exclusive' => true]],
			[0.5, [0.5, 1.5, 'min_exclusive' => true]],
			[1.75, [0.5, 1.5, 'min_exclusive' => true]],
			[-2.5, [-2, -0.5, 'min_exclusive' => true]],
			[-2, [-2, -0.5, 'min_exclusive' => true]],
			[-0.3, [-2, -0.5, 'min_exclusive' => true]],
			[74, [75, 123, 'min_exclusive' => true]],
			[75, [75, 123, 'min_exclusive' => true]],
			[124, [75, 123, 'min_exclusive' => true]],
			[-204.9, [-204.375, -123.456, 'min_exclusive' => true]],
			[-204.375, [-204.375, -123.456, 'min_exclusive' => true]],
			[-123.1, [-204.375, -123.456, 'min_exclusive' => true]],
			[-1, [0, 0, 'max_exclusive' => true]],
			[0, [0, 0, 'max_exclusive' => true]],
			[1, [0, 0, 'max_exclusive' => true]],
			[-1, [0, 2, 'max_exclusive' => true]],
			[2, [0, 2, 'max_exclusive' => true]],
			[3, [0, 2, 'max_exclusive' => true]],
			[0.25, [0.5, 1.5, 'max_exclusive' => true]],
			[1.5, [0.5, 1.5, 'max_exclusive' => true]],
			[1.75, [0.5, 1.5, 'max_exclusive' => true]],
			[-2.5, [-2, -0.5, 'max_exclusive' => true]],
			[-0.5, [-2, -0.5, 'max_exclusive' => true]],
			[-0.3, [-2, -0.5, 'max_exclusive' => true]],
			[74, [75, 123, 'max_exclusive' => true]],
			[123, [75, 123, 'max_exclusive' => true]],
			[124, [75, 123, 'max_exclusive' => true]],
			[-204.9, [-204.375, -123.456, 'max_exclusive' => true]],
			[-123.1, [-204.375, -123.456, 'max_exclusive' => true]],
			[-123.456, [-204.375, -123.456, 'max_exclusive' => true]],
			[-1, [0, 0, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0, [0, 0, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1, [0, 0, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-1, [0, 2, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0, [0, 2, 'min_exclusive' => true, 'max_exclusive' => true]],
			[2, [0, 2, 'min_exclusive' => true, 'max_exclusive' => true]],
			[3, [0, 2, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0.25, [0.5, 1.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0.5, [0.5, 1.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1.5, [0.5, 1.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1.75, [0.5, 1.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-2.5, [-2, -0.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-2, [-2, -0.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-0.5, [-2, -0.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-0.3, [-2, -0.5, 'min_exclusive' => true, 'max_exclusive' => true]],
			[74, [75, 123, 'min_exclusive' => true, 'max_exclusive' => true]],
			[75, [75, 123, 'min_exclusive' => true, 'max_exclusive' => true]],
			[123, [75, 123, 'min_exclusive' => true, 'max_exclusive' => true]],
			[124, [75, 123, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-204.9, [-204.375, -123.456, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-204.375, [-204.375, -123.456, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-123.456, [-204.375, -123.456, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-123.1, [-204.375, -123.456, 'min_exclusive' => true, 'max_exclusive' => true]],
			[0, [0, 0, 'negate' => true]],
			[0, [0, 2, 'negate' => true]],
			[1, [0, 2, 'negate' => true]],
			[2, [0, 2, 'negate' => true]],
			[0.5, [0.5, 1.5, 'negate' => true]],
			[1, [0.5, 1.5, 'negate' => true]],
			[1.5, [0.5, 1.5, 'negate' => true]],
			[-2, [-2, -0.5, 'negate' => true]],
			[-1, [-2, -0.5, 'negate' => true]],
			[-0.5, [-2, -0.5, 'negate' => true]],
			[75, [75, 123, 'negate' => true]],
			[100, [75, 123, 'negate' => true]],
			[123, [75, 123, 'negate' => true]],
			[-204.375, [-204.375, -123.456, 'negate' => true]],
			[-204.1, [-204.375, -123.456, 'negate' => true]],
			[-150.777, [-204.375, -123.456, 'negate' => true]],
			[-123.9, [-204.375, -123.456, 'negate' => true]],
			[-123.456, [-204.375, -123.456, 'negate' => true]],
			[1, [0, 2, 'negate' => true, 'min_exclusive' => true]],
			[2, [0, 2, 'negate' => true, 'min_exclusive' => true]],
			[1, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true]],
			[1.5, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true]],
			[-1, [-2, -0.5, 'negate' => true, 'min_exclusive' => true]],
			[-0.5, [-2, -0.5, 'negate' => true, 'min_exclusive' => true]],
			[100, [75, 123, 'negate' => true, 'min_exclusive' => true]],
			[123, [75, 123, 'negate' => true, 'min_exclusive' => true]],
			[-204.1, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-150.777, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-123.9, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[-123.456, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true]],
			[0, [0, 2, 'negate' => true, 'max_exclusive' => true]],
			[1, [0, 2, 'negate' => true, 'max_exclusive' => true]],
			[0.5, [0.5, 1.5, 'negate' => true, 'max_exclusive' => true]],
			[1, [0.5, 1.5, 'negate' => true, 'max_exclusive' => true]],
			[-2, [-2, -0.5, 'negate' => true, 'max_exclusive' => true]],
			[-1, [-2, -0.5, 'negate' => true, 'max_exclusive' => true]],
			[75, [75, 123, 'negate' => true, 'max_exclusive' => true]],
			[100, [75, 123, 'negate' => true, 'max_exclusive' => true]],
			[-204.375, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-204.1, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-150.777, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[-123.9, [-204.375, -123.456, 'negate' => true, 'max_exclusive' => true]],
			[1, [0, 2, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[1, [0.5, 1.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-1, [-2, -0.5, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[100, [75, 123, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-204.1, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-150.777, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]],
			[-123.9, [-204.375, -123.456, 'negate' => true, 'min_exclusive' => true, 'max_exclusive' => true]]
		];
	}
	
	/**
	 * Test `ExplanationProducer` interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 * 
	 * @return void
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [0, 1])->getExplanation());
	}
}
