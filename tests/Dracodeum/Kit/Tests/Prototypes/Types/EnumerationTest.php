<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Enumeration as Prototype;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enumeration;
use Dracodeum\Kit\Options\Text as TextOptions;
use stdClass;

/** @covers \Dracodeum\Kit\Prototypes\Types\Enumeration */
class EnumerationTest extends TestCase
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
	 */
	public function testProcess(mixed $value, mixed $expected, array $properties): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($expected, $value);
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
	 */
	public function testProcess_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Test process (non-internal).
	 * 
	 * @testdox Process (non-internal)
	 * @dataProvider provideProcessData_NonInternal
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_NonInternal(mixed $value, mixed $expected, array $properties): void
	{
		//components
		$components = [
			Component::build(Prototype::class, $properties),
			Component::build(Prototype::class, ['strict' => true] + $properties)
		];
		
		//assert
		foreach ($components as $component) {
			foreach (EContext::getValues() as $context) {
				if ($context !== EContext::INTERNAL) {
					$v = $value;
					$this->assertNull($component->process($v, $context));
					$this->assertSame($expected, $v);
				}
			}
		}
	}
	
	/**
	 * Test process (non-internal error).
	 * 
	 * @testdox Process (non-internal error)
	 * @dataProvider provideProcessData_NonInternal_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_NonInternal_Error(mixed $value, array $properties): void
	{
		//components
		$components = [
			Component::build(Prototype::class, $properties),
			Component::build(Prototype::class, ['strict' => true] + $properties)
		];
		
		//assert
		foreach ($components as $component) {
			foreach (EContext::getValues() as $context) {
				if ($context !== EContext::INTERNAL) {
					$this->assertInstanceOf(Error::class, $component->process($value, $context));
				}
			}
		}
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
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Strict(mixed $value, array $properties): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, ['strict' => true] + $properties)->process($v));
		$this->assertSame($value, $v);
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
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Strict_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(
			Error::class, Component::build(Prototype::class, ['strict' => true] + $properties)->process($value)
		);
	}
	
	/**
	 * Test `Textifier` interface.
	 * 
	 * @testdox Textifier interface
	 * @dataProvider provideTextifierInterfaceData
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string $expected
	 * The expected textified value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testTextifierInterface(mixed $value, string $expected, array $properties): void
	{
		$text = Component::build(Prototype::class, $properties)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($expected, $text->toString());
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
			[1, 1, [EnumerationTest_Enum1::class]],
			[4, 4, [EnumerationTest_Enum1::class]],
			[9, 9, [EnumerationTest_Enum1::class]],
			['A', 1, [EnumerationTest_Enum1::class]],
			['B', 4, [EnumerationTest_Enum1::class]],
			['C', 9, [EnumerationTest_Enum1::class]],
			['foo', 'foo', [EnumerationTest_Enum2::class]],
			['bar', 'bar', [EnumerationTest_Enum2::class]],
			['FOO', 'foo', [EnumerationTest_Enum2::class]],
			['BAR', 'bar', [EnumerationTest_Enum2::class]]
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
			[null, [EnumerationTest_Enum1::class]],
			[false, [EnumerationTest_Enum1::class]],
			[true, [EnumerationTest_Enum1::class]],
			[0, [EnumerationTest_Enum1::class]],
			[10, [EnumerationTest_Enum1::class]],
			[-1, [EnumerationTest_Enum1::class]],
			[0.0, [EnumerationTest_Enum1::class]],
			[1.0, [EnumerationTest_Enum1::class]],
			[-1.0, [EnumerationTest_Enum1::class]],
			['', [EnumerationTest_Enum1::class]],
			[' ', [EnumerationTest_Enum1::class]],
			['foobar', [EnumerationTest_Enum1::class]],
			[[], [EnumerationTest_Enum1::class]],
			[new stdClass, [EnumerationTest_Enum1::class]],
			[fopen(__FILE__, 'r'), [EnumerationTest_Enum1::class]],
			[2, [EnumerationTest_Enum1::class]],
			['D', [EnumerationTest_Enum1::class]],
			[' A ', [EnumerationTest_Enum1::class]],
			['_B_', [EnumerationTest_Enum1::class]],
			[1, [EnumerationTest_Enum2::class]],
			['A', [EnumerationTest_Enum2::class]],
			['Foo', [EnumerationTest_Enum2::class]],
			['Bar', [EnumerationTest_Enum2::class]],
			['foo2', [EnumerationTest_Enum2::class]],
			['foobar', [EnumerationTest_Enum2::class]],
			[' FOO ', [EnumerationTest_Enum2::class]],
			['_BAR_', [EnumerationTest_Enum2::class]]
		];
	}
	
	/**
	 * Provide process data (non-internal).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_NonInternal(): array
	{
		return [
			['A', 1, [EnumerationTest_Enum1::class]],
			['B', 4, [EnumerationTest_Enum1::class]],
			['C', 9, [EnumerationTest_Enum1::class]],
			['FOO', 'foo', [EnumerationTest_Enum2::class]],
			['BAR', 'bar', [EnumerationTest_Enum2::class]]
		];
	}
	
	/**
	 * Provide process data (non-internal error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_NonInternal_Error(): array
	{
		return array_merge(self::provideProcessData_Error(), [
			[1, [EnumerationTest_Enum1::class]],
			[4, [EnumerationTest_Enum1::class]],
			[9, [EnumerationTest_Enum1::class]],
			['foo', [EnumerationTest_Enum2::class]],
			['bar', [EnumerationTest_Enum2::class]]
		]);
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
			[1, [EnumerationTest_Enum1::class]],
			[4, [EnumerationTest_Enum1::class]],
			[9, [EnumerationTest_Enum1::class]],
			['foo', [EnumerationTest_Enum2::class]],
			['bar', [EnumerationTest_Enum2::class]]
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
			['A', [EnumerationTest_Enum1::class]],
			['B', [EnumerationTest_Enum1::class]],
			['C', [EnumerationTest_Enum1::class]],
			['FOO', [EnumerationTest_Enum2::class]],
			['BAR', [EnumerationTest_Enum2::class]]
		];
	}
	
	/**
	 * Provide `Textifier` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideTextifierInterfaceData(): array
	{
		return [
			[1, 'A', [EnumerationTest_Enum1::class]],
			[4, 'B', [EnumerationTest_Enum1::class]],
			[9, 'C', [EnumerationTest_Enum1::class]],
			['foo', "Foo", [EnumerationTest_Enum2::class]],
			['bar', "Bar", [EnumerationTest_Enum2::class]]
		];
	}
}



/** Test case dummy enumeration 1. */
class EnumerationTest_Enum1 extends Enumeration
{
	public const A = 1;
	public const B = 4;
	public const C = 9;
}



/** Test case dummy enumeration 2. */
class EnumerationTest_Enum2 extends Enumeration
{
	public const FOO = 'foo';
	public const BAR = 'bar';
	
	protected static function returnLabel(string $name, TextOptions $text_options): ?string
	{
		return match ($name) {
			'FOO' => "Foo",
			'BAR' => "Bar",
			default => null
		};
	}
}
