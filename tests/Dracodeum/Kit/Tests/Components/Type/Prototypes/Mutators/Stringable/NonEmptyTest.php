<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringable;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\NonEmpty as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\NonEmpty */
class NonEmptyTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @dataProvider provideProcessData
	 * @testdox Process
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @return void
	 */
	public function testProcess(mixed $value): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($v, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * <p>The provided process data.</p>
	 */
	public function provideProcessData(): array
	{
		return [
			['0'],
			['foo'],
			[' ']
		];
	}
	
	/**
	 * Test process (error).
	 * 
	 * @dataProvider provideProcessData_Error
	 * @testdox Process (error)
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param array $properties [default = []]
	 * <p>The process properties parameter to test with.</p>
	 * @return void
	 */
	public function testProcess_Error(mixed $value, array $properties = []): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * <p>The provided process data (error).</p>
	 */
	public function provideProcessData_Error(): array
	{
		return [
			[''],
			[' ', ['ignore_whitespace' => true]]
		];
	}
	
	/**
	 * Test <code>Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer</code> interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 * @return void
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class)->getExplanation());
	}
}
