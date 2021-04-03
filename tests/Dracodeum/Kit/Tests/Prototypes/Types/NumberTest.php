<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Number as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Interfaces\{
	Integerable as IIntegerable,
	Floatable as IFloatable
};
use Dracodeum\Kit\Prototypes\Types\Number\Enumerations\Type as EType;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals as NumericalMutators;
use Dracodeum\Kit\Utilities\Type as UType;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Number */
class NumberTest extends TestCase
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
	 * The expected processed value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testProcess(mixed $value, mixed $expected, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($expected, $value);
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
			[false, 0],
			[true, 1],
			[0, 0],
			[0.0, 0],
			[123, 123],
			[-123, -123],
			[123.0, 123],
			[-123.0, -123],
			[0.123, 0.123],
			[-0.123, -0.123],
			[123.456, 123.456],
			[-123.456, -123.456],
			['0', 0],
			['0.0', 0],
			['123', 123],
			['-123', -123],
			['123.0', 123],
			['-123.0', -123],
			['.123', 0.123],
			['-.123', -0.123],
			['0.123', 0.123],
			['-0.123', -0.123],
			['123.456', 123.456],
			['-123.456', -123.456],
			['123k', 123000],
			['-123k', -123000],
			['123.0k', 123000],
			['-123.0k', -123000],
			['123.456k', 123456],
			['-123.456k', -123456],
			['123.4567k', 123456.7],
			['-123.4567k', -123456.7],
			[new NumberTest_Class1(), 123],
			[new NumberTest_Class2(), 456.789],
			[false, 0, ['type' => EType::INTEGER]],
			[true, 1, ['type' => EType::INTEGER]],
			[0, 0, ['type' => EType::INTEGER]],
			[0.0, 0, ['type' => EType::INTEGER]],
			[123, 123, ['type' => EType::INTEGER]],
			[-123, -123, ['type' => EType::INTEGER]],
			[123.0, 123, ['type' => EType::INTEGER]],
			[-123.0, -123, ['type' => EType::INTEGER]],
			['0', 0, ['type' => EType::INTEGER]],
			['0.0', 0, ['type' => EType::INTEGER]],
			['123', 123, ['type' => EType::INTEGER]],
			['-123', -123, ['type' => EType::INTEGER]],
			['123.0', 123, ['type' => EType::INTEGER]],
			['-123.0', -123, ['type' => EType::INTEGER]],
			['123k', 123000, ['type' => EType::INTEGER]],
			['-123k', -123000, ['type' => EType::INTEGER]],
			['123.0k', 123000, ['type' => EType::INTEGER]],
			['-123.0k', -123000, ['type' => EType::INTEGER]],
			['123.456k', 123456, ['type' => EType::INTEGER]],
			['-123.456k', -123456, ['type' => EType::INTEGER]],
			[new NumberTest_Class1(), 123, ['type' => EType::INTEGER]],
			[false, 0.0, ['type' => EType::FLOAT]],
			[true, 1.0, ['type' => EType::FLOAT]],
			[0, 0.0, ['type' => EType::FLOAT]],
			[0.0, 0.0, ['type' => EType::FLOAT]],
			[123, 123.0, ['type' => EType::FLOAT]],
			[-123, -123.0, ['type' => EType::FLOAT]],
			[123.0, 123.0, ['type' => EType::FLOAT]],
			[-123.0, -123.0, ['type' => EType::FLOAT]],
			[0.123, 0.123, ['type' => EType::FLOAT]],
			[-0.123, -0.123, ['type' => EType::FLOAT]],
			[123.456, 123.456, ['type' => EType::FLOAT]],
			[-123.456, -123.456, ['type' => EType::FLOAT]],
			['0', 0.0, ['type' => EType::FLOAT]],
			['0.0', 0.0, ['type' => EType::FLOAT]],
			['123', 123.0, ['type' => EType::FLOAT]],
			['-123', -123.0, ['type' => EType::FLOAT]],
			['123.0', 123.0, ['type' => EType::FLOAT]],
			['-123.0', -123.0, ['type' => EType::FLOAT]],
			['.123', 0.123, ['type' => EType::FLOAT]],
			['-.123', -0.123, ['type' => EType::FLOAT]],
			['0.123', 0.123, ['type' => EType::FLOAT]],
			['-0.123', -0.123, ['type' => EType::FLOAT]],
			['123.456', 123.456, ['type' => EType::FLOAT]],
			['-123.456', -123.456, ['type' => EType::FLOAT]],
			['123k', 123000.0, ['type' => EType::FLOAT]],
			['-123k', -123000.0, ['type' => EType::FLOAT]],
			['123.0k', 123000.0, ['type' => EType::FLOAT]],
			['-123.0k', -123000.0, ['type' => EType::FLOAT]],
			['123.456k', 123456.0, ['type' => EType::FLOAT]],
			['-123.456k', -123456.0, ['type' => EType::FLOAT]],
			['123.4567k', 123456.7, ['type' => EType::FLOAT]],
			['-123.4567k', -123456.7, ['type' => EType::FLOAT]],
			[new NumberTest_Class1(), 123.0, ['type' => EType::FLOAT]],
			[new NumberTest_Class2(), 456.789, ['type' => EType::FLOAT]]
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
	public function testProcess_Error(mixed $value, array $properties = []): void
	{
		$v = $value;
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($v));
		$this->assertSame($value, $v);
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
			[null],
			['foo'],
			['#123'],
			['123#'],
			['123A'],
			['foo123'],
			['123 456'],
			['123_456'],
			['123-456'],
			['0x123456'],
			[[]],
			[new stdClass()],
			[fopen(__FILE__, 'r')],
			[0.123, ['type' => EType::INTEGER]],
			[-0.123, ['type' => EType::INTEGER]],
			[123.456, ['type' => EType::INTEGER]],
			[-123.456, ['type' => EType::INTEGER]],
			['.123', ['type' => EType::INTEGER]],
			['-.123', ['type' => EType::INTEGER]],
			['0.123', ['type' => EType::INTEGER]],
			['-0.123', ['type' => EType::INTEGER]],
			['123.456', ['type' => EType::INTEGER]],
			['-123.456', ['type' => EType::INTEGER]],
			['123.4567k', ['type' => EType::INTEGER]],
			['-123.4567k', ['type' => EType::INTEGER]],
			[new NumberTest_Class2(), ['type' => EType::INTEGER]]
		];
	}
	
	/**
	 * Test `InformationProducer` interface.
	 * 
	 * @testdox InformationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer
	 * 
	 * @return void
	 */
	public function testInformationProducerInterface(): void
	{
		$component = Component::build(Prototype::class);
		$this->assertInstanceOf(Text::class, $component->getLabel());
		$this->assertInstanceOf(Text::class, $component->getDescription());
	}
	
	/**
	 * Test `MutatorProducer` interface.
	 * 
	 * @testdox MutatorProducer interface ("$name")
	 * @dataProvider provideMutatorProducerData
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $expected
	 * The expected produced class.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testMutatorProducerInterface(string $name, string $expected, array $properties = []): void
	{
		$mutator = (new Prototype())->produceMutator($name, $properties);
		$this->assertNotNull($mutator);
		$this->assertTrue(UType::isA($mutator, $expected));
	}
	
	/**
	 * Provide `MutatorProducer` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMutatorProducerData(): array
	{
		return [
			['minimum', NumericalMutators\Minimum::class, [0]],
			['min', NumericalMutators\Minimum::class, [0]],
			['xminimum', NumericalMutators\Minimum::class, [0]],
			['xmin', NumericalMutators\Minimum::class, [0]],
			['unsigned', NumericalMutators\Minimum::class],
			['positive', NumericalMutators\Minimum::class],
			['maximum', NumericalMutators\Maximum::class, [0]],
			['max', NumericalMutators\Maximum::class, [0]],
			['xmaximum', NumericalMutators\Maximum::class, [0]],
			['xmax', NumericalMutators\Maximum::class, [0]],
			['negative', NumericalMutators\Maximum::class],
			['range', NumericalMutators\Range::class, [0, 1]],
			['xrange', NumericalMutators\Range::class, [0, 1]],
			['non_range', NumericalMutators\Range::class, [0, 1]],
			['non_xrange', NumericalMutators\Range::class, [0, 1]],
			['odd', NumericalMutators\Odd::class],
			['even', NumericalMutators\Even::class],
			['multiples', NumericalMutators\Multiples::class, [[1]]],
			['non_multiples', NumericalMutators\Multiples::class, [[1]]]
		];
	}
}



/** Test case dummy class 1. */
class NumberTest_Class1 implements IIntegerable
{
	public function toInteger(): int
	{
		return 123;
	}
}



/** Test case dummy class 2. */
class NumberTest_Class2 implements IFloatable
{
	public function toFloat(): float
	{
		return 456.789;
	}
}
