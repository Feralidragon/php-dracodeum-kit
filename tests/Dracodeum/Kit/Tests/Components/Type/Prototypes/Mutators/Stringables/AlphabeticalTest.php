<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Alphabetical as Prototype;
use Dracodeum\Kit\Enumerations\TextCase as ETextCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Alphabetical */
class AlphabeticalTest extends TestCase
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
	 * 
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
			["F\u{03a9}\u{03a9}B\u{0393}R", ['case' => ETextCase::UPPER, 'unicode' => true]]
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
			$d[0] = new AlphabeticalTest_Class($d[0]);
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
	 * 
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
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		return [
			[' '],
			['_'],
			['!'],
			['123'],
			['foo bar'],
			['foo_bar'],
			['foobar123'],
			['FOO BAR'],
			['FOO_BAR'],
			['FOOBAR123'],
			['Foo Bar'],
			['Foo_Bar'],
			['FooBar123'],
			['FOOBAR', ['case' => ETextCase::LOWER]],
			['FooBar', ['case' => ETextCase::LOWER]],
			['foobar', ['case' => ETextCase::UPPER]],
			['FooBar', ['case' => ETextCase::UPPER]],
			[' ', ['unicode' => true]],
			['_', ['unicode' => true]],
			['!', ['unicode' => true]],
			['123', ['unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r"],
			["F\u{03a9}\u{03a9}B\u{0393}R"],
			["F\u{03a9}\u{03c9}B\u{0393}r"],
			["f\u{03c9}\u{03c9} b\u{03b3}r", ['unicode' => true]],
			["f\u{03c9}\u{03c9}_b\u{03b3}r", ['unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r123", ['unicode' => true]],
			["F\u{03a9}\u{03a9} B\u{0393}R", ['unicode' => true]],
			["F\u{03a9}\u{03a9}_B\u{0393}R", ['unicode' => true]],
			["F\u{03a9}\u{03a9}B\u{0393}R123", ['unicode' => true]],
			["F\u{03a9}\u{03c9} B\u{0393}r", ['unicode' => true]],
			["F\u{03a9}\u{03c9}_B\u{0393}r", ['unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r123", ['unicode' => true]],
			["F\u{03a9}\u{03a9}B\u{0393}R", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r", ['case' => ETextCase::LOWER, 'unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r", ['case' => ETextCase::UPPER, 'unicode' => true]],
			["F\u{03a9}\u{03c9}B\u{0393}r", ['case' => ETextCase::UPPER, 'unicode' => true]],
			["f\u{03c9}\u{03c9}b\u{03b3}r1\u{2161}3\u{2163}5\u{2165}", ['unicode' => true]]
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
			$d[0] = new AlphabeticalTest_Class($d[0]);
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
	 * 
	 * @return void
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class)->getExplanation());
	}
}



/** Test case dummy class. */
class AlphabeticalTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
