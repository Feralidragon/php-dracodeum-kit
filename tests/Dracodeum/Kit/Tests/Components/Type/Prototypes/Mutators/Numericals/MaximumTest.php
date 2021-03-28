<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Numericals;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Maximum as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Maximum */
class MaximumTest extends TestCase
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
			[0, [0]],
			[1, [2]],
			[1, [1.5]],
			[-0.5, [0]],
			[100, [123]],
			[123, [123]],
			[-200, [-123]],
			[-123, [-123]],
			[-124, [-123.456]],
			[-123.9, [-123.456]],
			[-123.456, [-123.456]],
			[123, [123.456]],
			[123.1, [123.456]],
			[123.456, [123.456]],
			[1, [2, 'exclusive' => true]],
			[1, [1.5, 'exclusive' => true]],
			[-0.5, [0, 'exclusive' => true]],
			[-124, [-123.456, 'exclusive' => true]],
			[-123.9, [-123.456, 'exclusive' => true]],
			[123, [123.456, 'exclusive' => true]],
			[123.1, [123.456, 'exclusive' => true]]
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
			[1, [0]],
			[1, [0.5]],
			[0, [-0.5]],
			[200, [123]],
			[-100, [-123]],
			[-123, [-123.456]],
			[-123.1, [-123.456]],
			[124, [123.456]],
			[123.9, [123.456]],
			[0, [0, 'exclusive' => true]],
			[123, [123, 'exclusive' => true]],
			[-123, [-123, 'exclusive' => true]],
			[-123.456, [-123.456, 'exclusive' => true]],
			[123.456, [123.456, 'exclusive' => true]]
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
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [0])->getExplanation());
	}
}
