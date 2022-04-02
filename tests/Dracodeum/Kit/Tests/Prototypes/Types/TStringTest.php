<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TString as Prototype;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables as StringableMutators;
use Dracodeum\Kit\Utilities\Type as UType;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TString */
class TStringTest extends TestCase
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
	 * @return void
	 */
	public function testProcess(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
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
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @return void
	 */
	public function testProcess_Error(mixed $value): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class)->process($value));
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
	 * @testdox Process (Unicode)
	 * @dataProvider provideProcessData_Unicode
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 * 
	 * @return void
	 */
	public function testProcess_Unicode(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class, ['unicode' => true])->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Provide process data (unicode).
	 * 
	 * @return array
	 * The data.
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
	 * Test process (strict).
	 * 
	 * @testdox Process (strict)
	 * @dataProvider provideProcessData_Strict
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @return void
	 */
	public function testProcess_Strict(mixed $value): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, ['strict' => true])->process($v));
		$this->assertSame($value, $v);
	}
	
	/**
	 * Provide process data (strict).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Strict(): array
	{
		return [
			[''],
			[' '],
			['0'],
			['foo'],
			['foo Bar'],
			["premi\xC3\xA9re"],
			["premi\xE9re"]
		];
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
	 * @return void
	 */
	public function testProcess_Strict_Error(mixed $value): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, ['strict' => true])->process($value));
	}
	
	/**
	 * Provide process data (strict, error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Strict_Error(): array
	{
		return [
			[0],
			[1],
			[0.0],
			[1.0],
			[7.5],
			[-179.248],
			[new TStringTest_Class1()],
			[new TStringTest_Class2()]
		];
	}
	
	/**
	 * Test `MutatorProducer` interface.
	 * 
	 * @testdox MutatorProducer interface ("$name")
	 * @dataProvider provideMutatorProducerData
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $expected
	 * The expected produced class.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testMutatorProducerInterface(string $name, string $expected, array $properties = []): void
	{
		$mutator = (new Prototype())->produceMutator($name, $properties);
		$this->assertNotNull($mutator);
		$this->assertTrue(UType::isA($mutator, $expected));
	}
	
	/**
	 * Provide `MutatorProducer` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideMutatorProducerData(): array
	{
		return [
			['length', StringableMutators\Length::class, [10]],
			['length_range', StringableMutators\LengthRange::class, [5, 10]],
			['min_length', StringableMutators\MinLength::class, [10]],
			['max_length', StringableMutators\MaxLength::class, [10]],
			['truncate', StringableMutators\Truncate::class, [10]],
			['non_empty', StringableMutators\NonEmpty::class],
			['non_empty_iws', StringableMutators\NonEmpty::class],
			['trim', StringableMutators\Trim::class],
			['empty_null', StringableMutators\EmptyNull::class],
			['lowercase', StringableMutators\Lowercase::class],
			['lower', StringableMutators\Lowercase::class],
			['uppercase', StringableMutators\Uppercase::class],
			['upper', StringableMutators\Uppercase::class],
			['to_lowercase', StringableMutators\ToLowercase::class],
			['to_lower', StringableMutators\ToLowercase::class],
			['to_uppercase', StringableMutators\ToUppercase::class],
			['to_upper', StringableMutators\ToUppercase::class],
			['hexadecimal', StringableMutators\Hexadecimal::class],
			['base64', StringableMutators\Base64::class],
			['alphabetical', StringableMutators\Alphabetical::class],
			['alphabetic', StringableMutators\Alphabetical::class],
			['lower_alphabetical', StringableMutators\Alphabetical::class],
			['lower_alphabetic', StringableMutators\Alphabetical::class],
			['upper_alphabetical', StringableMutators\Alphabetical::class],
			['upper_alphabetic', StringableMutators\Alphabetical::class],
			['numerical', StringableMutators\Numerical::class],
			['numeric', StringableMutators\Numerical::class],
			['alphanumerical', StringableMutators\Alphanumerical::class],
			['alphanumeric', StringableMutators\Alphanumerical::class],
			['lower_alphanumerical', StringableMutators\Alphanumerical::class],
			['lower_alphanumeric', StringableMutators\Alphanumerical::class],
			['upper_alphanumerical', StringableMutators\Alphanumerical::class],
			['upper_alphanumeric', StringableMutators\Alphanumerical::class],
			['identifier', StringableMutators\Identifier::class],
			['xidentifier', StringableMutators\Identifier::class],
			['lower_identifier', StringableMutators\Identifier::class],
			['upper_identifier', StringableMutators\Identifier::class],
			['lower_xidentifier', StringableMutators\Identifier::class],
			['upper_xidentifier', StringableMutators\Identifier::class],
			['wildcards', StringableMutators\Wildcards::class, [['*']]],
			['iwildcards', StringableMutators\Wildcards::class, [['*']]],
			['non_wildcards', StringableMutators\Wildcards::class, [['*']]],
			['non_iwildcards', StringableMutators\Wildcards::class, [['*']]]
		];
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
