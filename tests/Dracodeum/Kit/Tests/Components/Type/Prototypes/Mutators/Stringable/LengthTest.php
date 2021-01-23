<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringable;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\Length as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable\Length */
class LengthTest extends TestCase
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
	 * @param array $properties
	 * <p>The process properties parameter to test with.</p>
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
	 * <p>The provided process data.</p>
	 */
	public function provideProcessData(): array
	{
		return [
			['a', [1]],
			['  ', [2]],
			['foo', [3]],
			['Foo Bar', [7]],
			["foo\u{2003}b\u{01d4b6}r", [12]],
			["foo\u{2003}b\u{01d4b6}r", [7, 'unicode' => true]]
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
	 * @param array $properties
	 * <p>The process properties parameter to test with.</p>
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
	 * <p>The provided process data (error).</p>
	 */
	public function provideProcessData_Error(): array
	{
		return [
			['', [1]],
			['  ', [1]],
			['foo', [2]],
			['foo', [4]],
			['Foo Bar', [5]],
			['Foo Bar', [9]],
			["foo\u{2003}b\u{01d4b6}r", [6]],
			["foo\u{2003}b\u{01d4b6}r", [7]],
			["foo\u{2003}b\u{01d4b6}r", [8]],
			["foo\u{2003}b\u{01d4b6}r", [11, 'unicode' => true]],
			["foo\u{2003}b\u{01d4b6}r", [12, 'unicode' => true]],
			["foo\u{2003}b\u{01d4b6}r", [13, 'unicode' => true]]
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
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [10])->getExplanation());
	}
}
