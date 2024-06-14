<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TArray as Prototype;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Prototypes\Type as TypePrototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Interfaces\Arrayable as IArrayable;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables as CountableMutators;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Utilities\Type as UType;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TArray */
class TArrayTest extends TestCase
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
	public function testProcess(mixed $value, mixed $expected, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_Error_Internal
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
	 * Test process (non-internal).
	 * 
	 * @testdox Process (non-internal)
	 * @dataProvider provideProcessData
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
	public function testProcess_NonInternal(mixed $value, mixed $expected, array $properties = []): void
	{
		$component = Component::build(Prototype::class, $properties);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$v = $value;
				$this->assertNull($component->process($v, $context));
				$this->assertSame($expected, $v);
			}
		}
	}
	
	/**
	 * Test process (non-internal, error).
	 * 
	 * @testdox Process (non-internal, error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_NonInternal_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_NonInternal_Error(mixed $value, array $properties = []): void
	{
		$component = Component::build(Prototype::class, $properties);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$this->assertInstanceOf(Error::class, $component->process($value, $context));
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
	 * @param mixed $expected
	 * The expected processed value.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Strict(mixed $value, mixed $expected, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, ['strict' => true] + $properties)->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Test process (strict, error).
	 * 
	 * @testdox Process (strict, error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_Error_Internal
	 * @dataProvider provideProcessData_Strict_Error
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Strict_Error(mixed $value, array $properties = []): void
	{
		$this->assertInstanceOf(
			Error::class, Component::build(Prototype::class, ['strict' => true] + $properties)->process($value)
		);
	}
	
	/**
	 * Test process (non-internal, error, strict).
	 * 
	 * @testdox Process (non-internal, error, strict)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_NonInternal_Error
	 * @dataProvider provideProcessData_NonInternal_Error_Strict
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_NonInternal_Error_Strict(mixed $value, array $properties = []): void
	{
		$component = Component::build(Prototype::class, ['strict' => true] + $properties);
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$this->assertInstanceOf(Error::class, $component->process($value, $context));
			}
		}
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
	 * 
	 * @param \Dracodeum\Kit\Enums\Info\Level $info_level
	 * The info level to test with.
	 */
	public function testTextifierInterface(
		mixed $value, string $expected, array $properties = [], EInfoLevel $info_level = EInfoLevel::ENDUSER
	): void
	{
		$text = Component::build(Prototype::class, $properties)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($expected, $text->toString(['info_level' => $info_level->value]));
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
	 */
	public function testMutatorProducerInterface(string $name, string $expected, array $properties = []): void
	{
		$mutator = (new Prototype)->produceMutator($name, $properties);
		$this->assertNotNull($mutator);
		$this->assertTrue(UType::isA($mutator, $expected));
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
		//initialize
		$c1 = new TArrayTest_Class1;
		$c2 = new TArrayTest_Class2;
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			[[], []],
			[$c1, [123, 'foo', 'bar']],
			[$c2, ['a' => 123, 'b' => 'foo', 'bar']],
			[['foo', 'bar', 123], ['foo', 'bar', 123]],
			[['z' => 'foo', 'bar', 'x' => 123], ['z' => 'foo', 'bar', 'x' => 123]],
			[[], [], ['type' => $type1_class]],
			[$c1, ['#123', '*foo', '*bar'], ['type' => $type1_class]],
			[$c2, ['a' => '#123', 'b' => '*foo', '*bar'], ['type' => $type1_class]],
			[['foo', 'bar', 123], ['*foo', '*bar', '#123'], ['type' => $type1_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['z' => '*foo', '*bar', 'x' => '#123'], ['type' => $type1_class]],
			[[], [], ['key_type' => $type2_class]],
			[$c1, ['!0' => 123, '!1' => 'foo', '!2' => 'bar'], ['key_type' => $type2_class]],
			[$c2, ['_a' => 123, '_b' => 'foo', '!0' => 'bar'], ['key_type' => $type2_class]],
			[['foo', 'bar', 123], ['!0' => 'foo', '!1' => 'bar', '!2' => 123], ['key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['_z' => 'foo', '!0' => 'bar', '_x' => 123],
				['key_type' => $type2_class]],
			[[], [], ['type' => $type1_class, 'key_type' => $type2_class]],
			[$c1, ['!0' => '#123', '!1' => '*foo', '!2' => '*bar'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[$c2, ['_a' => '#123', '_b' => '*foo', '!0' => '*bar'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[['foo', 'bar', 123], ['!0' => '*foo', '!1' => '*bar', '!2' => '#123'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['_z' => '*foo', '!0' => '*bar', '_x' => '#123'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[[], [], ['non_associative' => true]],
			[$c1, [123, 'foo', 'bar'], ['non_associative' => true]],
			[['foo', 'bar', 123], ['foo', 'bar', 123], ['non_associative' => true]]
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
		//initialize
		$c1 = new TArrayTest_Class1;
		$c2 = new TArrayTest_Class2;
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			[null],
			[false],
			[true],
			[1],
			[1.1],
			[new stdClass],
			[fopen(__FILE__, 'r')],
			[$c1, ['type' => $type2_class]],
			[$c2, ['type' => $type2_class]],
			[['foooo', 'bar', 123], ['type' => $type1_class]],
			[['foo', '_BBB_', 123], ['type' => $type1_class]],
			[['foo', 'bar', 25500], ['type' => $type1_class]],
			[['foo', 'bar', '123'], ['type' => $type1_class]],
			[['z' => 'foooo', 'bar', 'x' => 123], ['type' => $type1_class]],
			[['z' => 'foo', '_BBB_', 'x' => 123], ['type' => $type1_class]],
			[['z' => 'foo', 'bar', 'x' => 25500], ['type' => $type1_class]],
			[['z' => 'foo', 'bar', 'x' => '123'], ['type' => $type1_class]],
			[$c1, ['key_type' => $type1_class]],
			[$c2, ['key_type' => $type1_class]],
			[[5 => 'foo', 'bar', 123], ['key_type' => $type2_class]],
			[['foo', 6 => 'bar', 123], ['key_type' => $type2_class]],
			[['foo', 'bar', 7 => 123], ['key_type' => $type2_class]],
			[['aaaaaa' => 'foo', 'bar', 'x' => 123], ['key_type' => $type2_class]],
			[['z' => 'foo', 5 => 'bar', 'x' => 123], ['key_type' => $type2_class]],
			[['z' => 'foo', 'bar', '@xCXx_' => 123], ['key_type' => $type2_class]],
			[['A' => 'foo', 'bar', 'a' => 123], ['key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'Z' => 123], ['key_type' => $type2_class]],
			[$c2, ['non_associative' => true]],
			[['z' => 'foo', 'bar', 'x' => 123], ['non_associative' => true]]
		];
	}
	
	/**
	 * Provide process data (error, internal).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error_Internal(): array
	{
		return [
			[''],
			[' '],
			['123'],
			['foo']
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
		//initialize
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			['', []],
			[' ', []],
			[',', ['', '']],
			[' , ', ['', '']],
			[':', ['' => '']],
			[' : ', ['' => '']],
			[':,', ['' => '', '']],
			[' : , ', ['' => '', '']],
			['foo,bar,456', ['foo', 'bar', '456']],
			['0:foo,1:bar,2:456', ['foo', 'bar', '456']],
			["  foo ,\nbar\t,  \t456\t\n", ['foo', 'bar', '456']],
			["  foo bar ,\nbar\t,  \t456\t\n", ['foo bar', 'bar', '456']],
			['z:foo,bar,x:456', ['z' => 'foo', 'bar', 'x' => '456']],
			["\tz\t:\n\nfoo   ,bar \n,\tx  :\n \t456\n  ", ['z' => 'foo', 'bar', 'x' => '456']],
			["\tz x2\t:\n\nfoo   ,bar \n,\tx  :\n \t45 67\n  ", ['z x2' => 'foo', 'bar', 'x' => '45 67']],
			['', [], ['type' => $type1_class]],
			['foo,bar,456', ['*foo', '*bar', '#456'], ['type' => $type1_class]],
			['z:foo,bar,x:456', ['z' => '*foo', '*bar', 'x' => '#456'], ['type' => $type1_class]],
			['', [], ['key_type' => $type2_class]],
			['foo,bar,456', ['!0' => 'foo', '!1' => 'bar', '!2' => '456'], ['key_type' => $type2_class]],
			['z:foo,bar,x:456', ['_z' => 'foo', '!0' => 'bar', '_x' => '456'], ['key_type' => $type2_class]],
			['', [], ['type' => $type1_class, 'key_type' => $type2_class]],
			['foo,bar,456', ['!0' => '*foo', '!1' => '*bar', '!2' => '#456'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			['z:foo,bar,x:456', ['_z' => '*foo', '!0' => '*bar', '_x' => '#456'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			['foo,bar,456', ['foo', 'bar', '456'], ['non_associative' => true]],
			['0:foo,1:bar,2:456', ['0:foo', '1:bar', '2:456'], ['non_associative' => true]]
		];
	}
	
	/**
	 * Provide process data (non-internal, error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_NonInternal_Error(): array
	{
		//initialize
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			['foo,bar,0:456'],
			['a:foo,bar,a:456'],
			['foooo,bar,456', ['type' => $type1_class]],
			['foo,_BBB_,456', ['type' => $type1_class]],
			['foo,bar,25500', ['type' => $type1_class]],
			['z:foooo,bar,x:456', ['type' => $type1_class]],
			['z:foo,_BBB_,x:456', ['type' => $type1_class]],
			['z:foo,bar,x:25500', ['type' => $type1_class]],
			['5:foo,bar,456', ['key_type' => $type2_class]],
			['foo,6:bar,456', ['key_type' => $type2_class]],
			['foo,bar,7:456', ['key_type' => $type2_class]],
			['aaaaaa:foo,bar,x:456', ['key_type' => $type2_class]],
			['z:foo,5100:bar,x:456', ['key_type' => $type2_class]],
			['z:foo,bar,@xCXx_:456', ['key_type' => $type2_class]],
			['A:foo,bar,a:456', ['key_type' => $type2_class]],
			['z:foo,bar,Z:456', ['key_type' => $type2_class]]
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
		//initialize
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			[[], []],
			[['foo', 'bar', 123], ['foo', 'bar', 123]],
			[['z' => 'foo', 'bar', 'x' => 123], ['z' => 'foo', 'bar', 'x' => 123]],
			[[], [], ['type' => $type1_class]],
			[['foo', 'bar', 123], ['*foo', '*bar', '#123'], ['type' => $type1_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['z' => '*foo', '*bar', 'x' => '#123'], ['type' => $type1_class]],
			[[], [], ['key_type' => $type2_class]],
			[['foo', 'bar', 123], ['!0' => 'foo', '!1' => 'bar', '!2' => 123], ['key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['_z' => 'foo', '!0' => 'bar', '_x' => 123],
				['key_type' => $type2_class]],
			[[], [], ['type' => $type1_class, 'key_type' => $type2_class]],
			[['foo', 'bar', 123], ['!0' => '*foo', '!1' => '*bar', '!2' => '#123'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], ['_z' => '*foo', '!0' => '*bar', '_x' => '#123'],
				['type' => $type1_class, 'key_type' => $type2_class]],
			[[], [], ['non_associative' => true]],
			[['foo', 'bar', 123], ['foo', 'bar', 123], ['non_associative' => true]]
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
			[new TArrayTest_Class1],
			[new TArrayTest_Class2]
		];
	}
	
	/**
	 * Provide process data (non-internal, error, strict).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_NonInternal_Error_Strict(): array
	{
		return [
			[''],
			[' '],
			[','],
			[' , '],
			[':'],
			[' : '],
			[':,'],
			[' : , '],
			['foo,bar,456'],
			['0:foo,1:bar,2:456']
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
		//initialize
		$type1_class = TArrayTest_TypePrototype1::class;
		$type2_class = TArrayTest_TypePrototype2::class;
		
		//return
		return [
			[[], ""],
			[[], "{}", [], EInfoLevel::TECHNICAL],
			[[], "[]", ['non_associative' => true], EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "0: foo,\n1: bar,\n2: 123"],
			[['foo', 'bar', 123], "{\n\t0: foo,\n\t1: bar,\n\t2: 123\n}", [], EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "foo, bar, 123", ['non_associative' => true]],
			[['foo', 'bar', 123], "[foo, bar, 123]", ['non_associative' => true], EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "0: -foo,\n1: -bar,\n2: +123", ['type' => $type1_class]],
			[['foo', 'bar', 123], "{\n\t0: -foo,\n\t1: -bar,\n\t2: +123\n}", ['type' => $type1_class],
				EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "?0: foo,\n?1: bar,\n?2: 123", ['key_type' => $type2_class]],
			[['foo', 'bar', 123], "{\n\t?0: foo,\n\t?1: bar,\n\t?2: 123\n}", ['key_type' => $type2_class],
				EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "?0: -foo,\n?1: -bar,\n?2: +123",
				['type' => $type1_class, 'key_type' => $type2_class]],
			[['foo', 'bar', 123], "{\n\t?0: -foo,\n\t?1: -bar,\n\t?2: +123\n}",
				['type' => $type1_class, 'key_type' => $type2_class], EInfoLevel::TECHNICAL],
			[['foo', 'bar', 123], "-foo, -bar, +123", ['type' => $type1_class, 'non_associative' => true]],
			[['foo', 'bar', 123], "[-foo, -bar, +123]", ['type' => $type1_class, 'non_associative' => true],
				EInfoLevel::TECHNICAL],
			[['z' => 'foo', 'bar', 'x' => 123], "z: foo,\n0: bar,\nx: 123"],
			[['z' => 'foo', 'bar', 'x' => 123], "{\n\tz: foo,\n\t0: bar,\n\tx: 123\n}", [], EInfoLevel::TECHNICAL],
			[['z' => 'foo', 'bar', 'x' => 123], "z: -foo,\n0: -bar,\nx: +123", ['type' => $type1_class]],
			[['z' => 'foo', 'bar', 'x' => 123], "{\n\tz: -foo,\n\t0: -bar,\n\tx: +123\n}", ['type' => $type1_class],
				EInfoLevel::TECHNICAL],
			[['z' => 'foo', 'bar', 'x' => 123], "&z: foo,\n?0: bar,\n&x: 123", ['key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], "{\n\t&z: foo,\n\t?0: bar,\n\t&x: 123\n}", ['key_type' => $type2_class],
				EInfoLevel::TECHNICAL],
			[['z' => 'foo', 'bar', 'x' => 123], "&z: -foo,\n?0: -bar,\n&x: +123",
				['type' => $type1_class, 'key_type' => $type2_class]],
			[['z' => 'foo', 'bar', 'x' => 123], "{\n\t&z: -foo,\n\t?0: -bar,\n\t&x: +123\n}",
				['type' => $type1_class, 'key_type' => $type2_class], EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA", "123"], "0: \n\tfoo\n\tbar,\n\n1: AAA,\n2: 123"],
			[["foo\nbar", "AAA", "123"], "{\n\t0: \n\t\tfoo\n\t\tbar,\n\t\n\t1: AAA,\n\t2: 123\n}", [],
				EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA\nBB", "123"], "0: \n\tfoo\n\tbar,\n\n1: \n\tAAA\n\tBB,\n\n2: 123"],
			[["foo\nbar", "AAA\nBB", "123"],
				"{\n\t0: \n\t\tfoo\n\t\tbar,\n\t\n\t1: \n\t\tAAA\n\t\tBB,\n\t\n\t2: 123\n}", [], EInfoLevel::TECHNICAL],
			[["foo", "AAA\nBB", "123"], "0: foo,\n\n1: \n\tAAA\n\tBB,\n\n2: 123"],
			[["foo", "AAA\nBB", "123"], "{\n\t0: foo,\n\t\n\t1: \n\t\tAAA\n\t\tBB,\n\t\n\t2: 123\n}", [],
				EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA", "123\n45"], "0: \n\tfoo\n\tbar,\n\n1: AAA,\n\n2: \n\t123\n\t45"],
			[["foo\nbar", "AAA", "123\n45"],
				"{\n\t0: \n\t\tfoo\n\t\tbar,\n\t\n\t1: AAA,\n\t\n\t2: \n\t\t123\n\t\t45\n}", [], EInfoLevel::TECHNICAL],
			[["foo", "AAA", "123\n45"], "0: foo,\n1: AAA,\n\n2: \n\t123\n\t45"],
			[["foo", "AAA", "123\n45"], "{\n\t0: foo,\n\t1: AAA,\n\t\n\t2: \n\t\t123\n\t\t45\n}", [],
				EInfoLevel::TECHNICAL],
			[["a\nx" => "foo", "AAA", "123"], "a\nx: foo,\n\n0: AAA,\n1: 123"],
			[["a\nx" => "foo", "AAA", "123"], "{\n\ta\n\tx: foo,\n\t\n\t0: AAA,\n\t1: 123\n}", [],
				EInfoLevel::TECHNICAL],
			[["a\nx" => "foo", "AAA", "z\nb" => "123"], "a\nx: foo,\n\n0: AAA,\n\nz\nb: 123"],
			[["a\nx" => "foo", "AAA", "z\nb" => "123"], "{\n\ta\n\tx: foo,\n\t\n\t0: AAA,\n\t\n\tz\n\tb: 123\n}", [],
				EInfoLevel::TECHNICAL],
			[["ax" => "foo", "AAA", "z\nb" => "123\n45"], "ax: foo,\n0: AAA,\n\nz\nb: \n\t123\n\t45"],
			[["ax" => "foo", "AAA", "z\nb" => "123\n45"],
				"{\n\tax: foo,\n\t0: AAA,\n\t\n\tz\n\tb: \n\t\t123\n\t\t45\n}", [], EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA", "123"], "foo\nbar,\n\nAAA,\n123", ['non_associative' => true]],
			[["foo\nbar", "AAA", "123"], "[\n\tfoo\n\tbar,\n\t\n\tAAA,\n\t123\n]", ['non_associative' => true],
				EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA\nBB", "123"], "foo\nbar,\n\nAAA\nBB,\n\n123", ['non_associative' => true]],
			[["foo\nbar", "AAA\nBB", "123"], "[\n\tfoo\n\tbar,\n\t\n\tAAA\n\tBB,\n\t\n\t123\n]",
				['non_associative' => true], EInfoLevel::TECHNICAL],
			[["foo", "AAA\nBB", "123"], "foo,\n\nAAA\nBB,\n\n123", ['non_associative' => true]],
			[["foo", "AAA\nBB", "123"], "[\n\tfoo,\n\t\n\tAAA\n\tBB,\n\t\n\t123\n]", ['non_associative' => true],
				EInfoLevel::TECHNICAL],
			[["foo\nbar", "AAA", "123\n45"], "foo\nbar,\n\nAAA,\n\n123\n45", ['non_associative' => true]],
			[["foo\nbar", "AAA", "123\n45"], "[\n\tfoo\n\tbar,\n\t\n\tAAA,\n\t\n\t123\n\t45\n]",
				['non_associative' => true], EInfoLevel::TECHNICAL],
			[["foo", "AAA", "123\n45"], "foo,\nAAA,\n\n123\n45", ['non_associative' => true]],
			[["foo", "AAA", "123\n45"], "[\n\tfoo,\n\tAAA,\n\t\n\t123\n\t45\n]", ['non_associative' => true],
				EInfoLevel::TECHNICAL]
		];
	}
	
	/**
	 * Provide `MutatorProducer` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideMutatorProducerData(): array
	{
		return [
			['non_empty', CountableMutators\NonEmpty::class],
			['empty_null', CountableMutators\EmptyNull::class],
			['count', CountableMutators\Count::class, [10]],
			['count_range', CountableMutators\CountRange::class, [5, 10]],
			['min_count', CountableMutators\MinCount::class, [10]],
			['max_count', CountableMutators\MaxCount::class, [10]]
		];
	}
}



/** Test case dummy class 1. */
class TArrayTest_Class1 implements IArrayable
{
	public function toArray(): array
	{
		return [123, 'foo', 'bar'];
	}
}



/** Test case dummy class 2. */
class TArrayTest_Class2 implements IArrayable
{
	public function toArray(): array
	{
		return ['a' => 123, 'b' => 'foo', 'bar'];
	}
}



/** Test case dummy type prototype 1. */
class TArrayTest_TypePrototype1 extends TypePrototype implements ITextifier
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (is_string($value) && $value !== '' && in_array($value[0], ['#', '*'] , true)) {
			return null;
		} elseif (in_array($value, [123, '456', 'foo', 'bar'], true)) {
			if ($value === '456') {
				$value = (int)$value;
			}
			$value = is_int($value) ? "#{$value}" : "*{$value}";
			return null;
		}
		return Error::build(text: "Must be 123, 456, foo or bar.");
	}
	
	public function textify(mixed $value)
	{
		return ($value[0] === '#' ? '+' : '-') . substr($value, 1);
	}
}



/** Test case dummy type prototype 2. */
class TArrayTest_TypePrototype2 extends TypePrototype implements ITextifier
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (is_string($value) && $value !== '' && in_array($value[0], ['!', '_'] , true)) {
			return null;
		} elseif ((is_int($value) && $value < 5) || in_array($value, ['a', 'A', 'b', 'B', 'x', 'X', 'z', 'Z'], true)) {
			$value = is_int($value) ? "!{$value}" : strtolower("_{$value}");
			return null;
		}
		return Error::build(text: "Must be an integer less than 5, or a, A, b, B, x, X, z or Z.");
	}
	
	public function textify(mixed $value)
	{
		return ($value[0] === '!' ? '?' : '&') . substr($value, 1);
	}
}
