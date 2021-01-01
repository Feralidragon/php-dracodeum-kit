<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TString as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TString */
class TStringTest extends TestCase
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
	 * @param string $expected
	 * <p>The expected processed value.</p>
	 * @return void
	 */
	public function testProcess(mixed $value, string $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
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
			[0, '0'],
			[1, '1'],
			[0.0, '0'],
			[1.0, '1'],
			[7.5, '7.5'],
			[-179.248, '-179.248'],
			['', ''],
			[' ', ' '],
			['0', '0'],
			['foo', 'foo'],
			['foo Bar', 'foo Bar'],
			["premi\xC3\xA9re", "premi\xC3\xA9re"],
			["premi\xE9re", "premi\xE9re"],
			[new TStringTest_Class1(), 'Class1'],
			[new TStringTest_Class2(), '__Class2']
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
	 * @return void
	 */
	public function testProcess_Error(mixed $value): void
	{
		$v = $value;
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class)->process($v));
		$this->assertSame($value, $v);
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
			[null],
			[false],
			[true],
			[[]],
			[new stdClass()],
			[fopen(__FILE__, 'r')]
		];
	}
	
	/**
	 * Test process (unicode).
	 * 
	 * @dataProvider provideProcessData_Unicode
	 * @testdox Process (Unicode)
	 * 
	 * @param mixed $value
	 * <p>The process value parameter to test with.</p>
	 * @param string $expected
	 * <p>The expected processed value.</p>
	 * @return void
	 */
	public function testProcess_Unicode(mixed $value, string $expected): void
	{
		$this->assertNull(Component::build(Prototype::class, ['unicode' => true])->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide process data (unicode).
	 * 
	 * @return array
	 * <p>The provided process data (unicode).</p>
	 */
	public function provideProcessData_Unicode(): array
	{
		return [
			['', ''],
			[' ', ' '],
			['0', '0'],
			['foo', 'foo'],
			['foo Bar', 'foo Bar'],
			["premi\xC3\xA9re", "premi\u{00E9}re"],
			["premi\xE9re", "premi\u{00E9}re"],
			["\u{3041}", "\u{3041}"],
			["\xE3\x81\x81", "\u{3041}"],
			["\xEF\xBB\xBF\xE3\x81\x81", "\u{3041}"]
		];
	}
	
	/**
	 * Test <code>Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer</code> interface.
	 * 
	 * @testdox InformationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer
	 * @return void
	 */
	public function testInformationProducerInterface(): void
	{
		$component = Component::build(Prototype::class);
		$this->assertInstanceOf(Text::class, $component->getLabel());
		$this->assertInstanceOf(Text::class, $component->getDescription());
	}
}



/** Test case dummy class 1. */
class TStringTest_Class1 implements IStringable
{
	public function toString($text_options = null): string
	{
		return "Class1";
	}
	
	public function __toString(): string
	{
		return "__Class1";
	}
}



/** Test case dummy class 2. */
class TStringTest_Class2
{
	public function __toString(): string
	{
		return "__Class2";
	}
}
