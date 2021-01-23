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
			['0'],
			['foo'],
			[' '],
			[" \n "],
			[" \n0\n ", ['ignore_whitespace' => true]],
			["\u{2003}", ['ignore_whitespace' => true]],
			["\u{2002} \n0\n \u{2003}", ['ignore_whitespace' => true, 'unicode' => true]]
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
			[''],
			[' ', ['ignore_whitespace' => true]],
			[" \n ", ['ignore_whitespace' => true]],
			[" \n\n ", ['ignore_whitespace' => true]],
			["\u{2003}", ['ignore_whitespace' => true, 'unicode' => true]],
			["\u{2002} \n\n \u{2003}", ['ignore_whitespace' => true, 'unicode' => true]],
			["\u{2002} \n\u{2003}\n \u{2002}", ['ignore_whitespace' => true, 'unicode' => true]]
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
