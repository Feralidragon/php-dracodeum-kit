<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringable;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\Uppercase as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\Uppercase */
class UppercaseTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param array $properties [default = []]
	 * <p>The process properties parameter to test with.</p>
	 * @return void
	 */
	public function testProcess(mixed $value, array $properties = []): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
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
			['A'],
			['FOO'],
			['123'],
			['FOO BAR123'],
			["\u{0393}"],
			["\u{03b3}"],
			["F\u{03c9}\u{03c9} \u{03b3}\u{03b4} \u{03be}123"],
			["F\u{03a9}\u{03a9} \u{0393}\u{0394} \u{039e}123", ['unicode' => true]]
		];
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
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
			['a'],
			['Foo'],
			['foo'],
			['FOO Bar123'],
			["f\u{03c9}\u{03c9} \u{03b3}\u{03b4} \u{03be}123"],
			["f\u{03a9}\u{03a9} \u{0393}\u{0394} \u{039e}123"],
			["F\u{03c9}\u{03c9} \u{03b3}\u{03b4} \u{03be}123", ['unicode' => true]],
			["F\u{03a9}\u{03a9} \u{03b3}\u{03b4} \u{03be}123", ['unicode' => true]]
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
