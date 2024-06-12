<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Text as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Text */
class TextTest extends TestCase
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
	 * @param string $expected_string
	 * The expected processed value string.
	 */
	public function testProcess(mixed $value, string $expected_string): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertInstanceOf(Text::class, $value);
		$this->assertSame($expected_string, $value->toString());
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testProcess_Error(mixed $value): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class)->process($value));
	}
	
	/**
	 * Test process (strict).
	 * 
	 * @testdox Process (strict)
	 * @dataProvider provideProcessData_Strict
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string $expected_string
	 * The expected processed value string.
	 */
	public function testProcess_Strict(mixed $value, string $expected_string): void
	{
		$this->assertNull(Component::build(Prototype::class, ['strict' => true])->process($value));
		$this->assertInstanceOf(Text::class, $value);
		$this->assertSame($expected_string, $value->toString());
	}
	
	/**
	 * Test process (strict, error).
	 * 
	 * @testdox Process (strict, error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_Strict_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testProcess_Strict_Error(mixed $value): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, ['strict' => true])->process($value));
	}
	
	/**
	 * Test `Textifier` interface.
	 * 
	 * @testdox Textifier interface
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 */
	public function testTextifierInterface(): void
	{
		$value = Text::build();
		$text = Component::build(Prototype::class)->textify($value);
		$this->assertSame($value, $text);
	}
	
	
	
	//Public static methods
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData(): array
	{
		return [
			['', ''],
			[' ', ' '],
			['0', '0'],
			['123', '123'],
			['foo', 'foo'],
			['foo Bar', 'foo Bar'],
			[Text::build('foo bar 123'), 'foo bar 123'],
			[new TextTest_Class1('foo 123 BAR'), 'foo 123 BAR'],
			[new TextTest_Class2('FOO 1 BAR 23'), 'FOO 1 BAR 23']
		];
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error(): array
	{
		return [
			[null],
			[false],
			[true],
			[1],
			[1.1],
			[[]],
			[new stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
	
	/**
	 * Provide process data (strict).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Strict(): array
	{
		return [
			[Text::build('foo bar 123'), 'foo bar 123']
		];
	}
	
	/**
	 * Provide process data (strict, error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Strict_Error(): array
	{
		return [
			[''],
			[' '],
			['0'],
			['123'],
			['foo'],
			['foo Bar'],
			[new TextTest_Class1('foo 123 BAR')],
			[new TextTest_Class2('FOO 1 BAR 23')]
		];
	}
}



/** Test case dummy class 1. */
class TextTest_Class1 implements IStringable
{
	public function __construct(private string $string) {}
	
	public function toString($text_options = null): string
	{
		return $this->string;
	}
}



/** Test case dummy class 2. */
class TextTest_Class2
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
