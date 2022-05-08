<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Type\Interfaces;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer as IMutatorProducer;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as MutatorPrototype;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/** @see \Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer */
class MutatorProducerTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * @dataProvider provideData
	 * 
	 * @param \Dracodeum\Kit\Components\Type $component
	 * The component instance to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 */
	public function test(Component $component, mixed $value, mixed $expected): void
	{
		$this->assertTrue($component->hasMutators());
		$this->assertNull($component->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideData(): array
	{
		return [[
			Component::build(MutatorProducerTest_Prototype::class)->addMutator('m1'), 35, 735.0
		], [
			Component::build(MutatorProducerTest_Prototype::class)->addMutator('m2', [1000]), 800, 1800.0
		], [
			Component::build(MutatorProducerTest_Prototype::class)->addMutator('m1')->addMutator('m2', [1000]),
			35, 1735.0
		], [
			Component::build(MutatorProducerTest_Prototype::class)
				->addMutator('m1', ['amount' => 850.5])
				->addMutator('m2', ['amount' => 37])
			, 48, 935.5
		], [
			Component::build(MutatorProducerTest_Prototype::class)
				->addMutator(MutatorProducerTest_MutatorPrototype1::class)
				->addMutator('m2', [1000])
			, 35, 1735.0
		], [
			Component::build(MutatorProducerTest_Prototype::class)
				->addMutator(MutatorProducerTest_MutatorPrototype1::class, ['amount' => 850.5])
				->addMutator('m2', ['amount' => 37])
			, 48, 935.5
		], [
			Component::build(MutatorProducerTest_Prototype::class)
				->addMutator('m1')
				->addMutator(MutatorProducerTest_MutatorPrototype2::class, [1000])
			, 35, 1735.0
		], [
			Component::build(MutatorProducerTest_Prototype::class)
				->addMutator('m1', ['amount' => 850.5])
				->addMutator(MutatorProducerTest_MutatorPrototype2::class, ['amount' => 37])
			, 48, 935.5
		]];
	}
	
	/**
	 * Test error.
	 * 
	 * @testdox Error
	 */
	public function testError(): void
	{
		//build
		$component = Component::build(MutatorProducerTest_Prototype::class)->addMutator('m1')->addMutator('m2', [1000]);
		
		//check
		$this->assertTrue($component->hasMutators());
		
		//error 1
		$value = $v = 65;
		$error = $component->process($value);
		$this->assertSame($v, $value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertSame(MutatorProducerTest_MutatorPrototype1::ERROR_STRING, (string)$error->getText());
		
		//error 2
		$value = $v = 15;
		$error = $component->process($value);
		$this->assertSame($v, $value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertNotSame('', (string)$error->getText());
		$this->assertNotSame(MutatorProducerTest_MutatorPrototype1::ERROR_STRING, (string)$error->getText());
	}
}



/** Test case dummy prototype class. */
class MutatorProducerTest_Prototype extends Prototype implements IMutatorProducer
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		return null;
	}
	
	public function produceMutator(string $name, array $properties)
	{
		return match ($name) {
			'm1' => MutatorProducerTest_MutatorPrototype1::class,
			'm2' => new MutatorProducerTest_MutatorPrototype2($properties),
			default => null
		};
	}
}



/** Test case dummy mutator prototype class 1. */
class MutatorProducerTest_MutatorPrototype1 extends MutatorPrototype
{
	public const ERROR_STRING = "Must be less than 50.";
	
	private float $amount = 700.0;
	
	public function process(mixed &$value): ?Error
	{
		$value = (float)$value;
		if ($value < 50.0) {
			$value += $this->amount;
			return null;
		}
		$value = -1;
		return Error::build(text: self::ERROR_STRING);
	}
	
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'amount' => $this->createProperty()->setAsFloat()->bind(self::class),
			default => null
		};
	}
}



/** Test case dummy mutator prototype class 2. */
class MutatorProducerTest_MutatorPrototype2 extends MutatorPrototype
{
	private float $amount;
	
	public function process(mixed &$value): ?Error
	{
		$value = (float)$value;
		if ($value > 725.0) {
			$value += $this->amount;
			return null;
		}
		$value = -1;
		return Error::build();
	}
	
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('amount');
	}
	
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'amount' => $this->createProperty()->setAsFloat()->bind(self::class),
			default => null
		};
	}
}
