<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Alphanumerical as Prototype;
use Dracodeum\Kit\Enumerations\TextCase as ETextCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Alphanumerical */
class AlphanumericalTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * @dataProvider provideProcessData_Class
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
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
	 * The data.
	 */
	public function provideProcessData(): array
	{
		return [
			[''],
			['foobar'],
			['FOOBAR'],
			['FooBar'],
			['foobar', ['case' => ETextCase::LOWER]],
			['FOOBAR', ['case' => ETextCase::UPPER]],
			["f\u{03c9}\u{03c9}b\u{03b3}r", ['unicode' => true]],
			["F\u{03a9}\u{03a9}B\u{0393}R", ['unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r", ['unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["F\u{03a9}\u{03a9}B\u{0393}R", ['case' => ETextCase::UPPER, 'unicode' => true]],
			['0'],
			['123'],
			['9102837465'],
			["\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1\u{2161}3\u{2163}5\u{2165}", ['unicode' => true]],
			['foobar123'],
			['123foobar'],
			['FOOBAR123'],
			['123FOOBAR'],
			['FooBar123'],
			['123FooBar'],
			['foobar123', ['case' => ETextCase::LOWER]],
			['123foobar', ['case' => ETextCase::LOWER]],
			['FOOBAR123', ['case' => ETextCase::UPPER]],
			['123FOOBAR', ['case' => ETextCase::UPPER]],
			["1f\u{03c9}\u{03c9}b\u{03b3}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03a9}B\u{0393}R\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03c9}B\u{0393}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1f\u{03c9}\u{03c9}b\u{03b3}r\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["1F\u{03a9}\u{03a9}B\u{0393}R\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::UPPER, 'unicode' => true]]
		];
	}
	
	/**
	 * Provide process data (class).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Class(): array
	{
		$data = $this->provideProcessData();
		foreach ($data as &$d) {
			$d[0] = new AlphanumericalTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_Error_Class
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, array $properties = []): void
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
			[' '],
			['_'],
			['!'],
			['foo bar'],
			['foo_bar'],
			['FOO BAR'],
			['FOO_BAR'],
			['Foo Bar'],
			['Foo_Bar'],
			['FOOBAR', ['case' => ETextCase::LOWER]],
			['FooBar', ['case' => ETextCase::LOWER]],
			['foobar', ['case' => ETextCase::UPPER]],
			['FooBar', ['case' => ETextCase::UPPER]],
			[' ', ['unicode' => true]],
			['_', ['unicode' => true]],
			['!', ['unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r"],
			["F\u{03a9}\u{03a9}B\u{0393}R"],
			["F\u{03a9}\u{03c9}B\u{0393}r"],
			["f\u{03c9}\u{03c9} b\u{03b3}r", ['unicode' => true]],
			["f\u{03c9}\u{03c9}_b\u{03b3}r", ['unicode' => true]],
			["F\u{03a9}\u{03a9} B\u{0393}R", ['unicode' => true]],
			["F\u{03a9}\u{03a9}_B\u{0393}R", ['unicode' => true]],
			["F\u{03a9}\u{03c9} B\u{0393}r", ['unicode' => true]],
			["F\u{03a9}\u{03c9}_B\u{0393}r", ['unicode' => true]],
			["F\u{03a9}\u{03a9}B\u{0393}R", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r", ['case' => ETextCase::UPPER, 'unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r", ['case' => ETextCase::UPPER, 'unicode' => true]],
			['123 456'],
			['123_456'],
			['123.456'],
			['123,456'],
			['-9102837465'],
			["\u{216d}\u{2169}\u{2166}"],
			["1\u{2161}3\u{2163}5\u{2165}"],
			["\u{216d} \u{2169} \u{2166}", ['unicode' => true]],
			["\u{216d}_\u{2169}_\u{2166}", ['unicode' => true]],
			["\u{216d}\u{2169}.\u{2166}", ['unicode' => true]],
			["\u{216d}\u{2169},\u{2166}", ['unicode' => true]],
			["-\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			['123 foo bar'],
			['foo 123 bar'],
			['foo bar 123'],
			['123_foo_bar'],
			['foo_123_bar'],
			['foo_bar_123'],
			['123 FOO BAR'],
			['FOO 123 BAR'],
			['FOO BAR 123'],
			['123_FOO_BAR'],
			['FOO_123_BAR'],
			['FOO_BAR_123'],
			['123 Foo Bar'],
			['Foo 123 Bar'],
			['Foo Bar 123'],
			['123_Foo_Bar'],
			['Foo_123_Bar'],
			['Foo_Bar_123'],
			['FOOBAR123', ['case' => ETextCase::LOWER]],
			['123FOOBAR', ['case' => ETextCase::LOWER]],
			['FooBar123', ['case' => ETextCase::LOWER]],
			['123FooBar', ['case' => ETextCase::LOWER]],
			['foobar123', ['case' => ETextCase::UPPER]],
			['123foobar', ['case' => ETextCase::UPPER]],
			['FooBar123', ['case' => ETextCase::UPPER]],
			['123FooBar', ['case' => ETextCase::UPPER]],
			["1f\u{03c9}\u{03c9}b\u{03b3}r\u{216d}\u{2169}\u{2166}"],
			["1F\u{03a9}\u{03a9}B\u{0393}R\u{216d}\u{2169}\u{2166}"],
			["1F\u{03a9}\u{03c9}B\u{0393}r\u{216d}\u{2169}\u{2166}"],
			["1f\u{03c9}\u{03c9} b\u{03b3}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1f\u{03c9}\u{03c9}_b\u{03b3}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03a9} B\u{0393}R\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03a9}_B\u{0393}R\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03c9} B\u{0393}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03c9}_B\u{0393}r\u{216d}\u{2169}\u{2166}", ['unicode' => true]],
			["1F\u{03a9}\u{03a9}B\u{0393}R\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["1F\u{03a9}\u{03c9}B\u{0393}r\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["1f\u{03c9}\u{03c9}b\u{03b3}r\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::UPPER, 'unicode' => true]],
			["1F\u{03a9}\u{03c9}B\u{0393}r\u{216d}\u{2169}\u{2166}", ['case' => ETextCase::UPPER, 'unicode' => true]]
		];
	}
	
	/**
	 * Provide process data (error, class).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error_Class(): array
	{
		$data = $this->provideProcessData_Error();
		foreach ($data as &$d) {
			$d[0] = new AlphanumericalTest_Class($d[0]);
		}
		unset($d);
		return $data;
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
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class)->getExplanation());
	}
}



/** Test case dummy class. */
class AlphanumericalTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
