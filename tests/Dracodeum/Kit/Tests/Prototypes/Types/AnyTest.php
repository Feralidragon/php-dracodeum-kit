<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Any as Prototype;
use Dracodeum\Kit\Prototypes\Type as TypePrototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier as ITextifier;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Any */
class AnyTest extends TestCase
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
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		//initialize
		$stdc = new stdClass;
		$r = fopen(__FILE__, 'r');
		$type1_class = AnyTest_TypePrototype1::class;
		$type2_class = AnyTest_TypePrototype2::class;
		
		//return
		return [
			[null, null],
			[false, false],
			[true, true],
			[1, 1],
			[1.1, 1.1],
			['foo', 'foo'],
			[[], []],
			[$stdc, $stdc],
			[$r, $r],
			[1, 1, ['types' => [$type1_class]]],
			[1.1, 1, ['types' => [$type1_class]]],
			[1, 1, ['types' => [$type1_class, $type2_class]]],
			[1, 1, ['types' => [$type2_class, $type1_class]]],
			[1.1, 1, ['types' => [$type1_class, $type2_class]]],
			[1.1, 1, ['types' => [$type2_class, $type1_class]]],
			['100', '100'],
			['100', 100, ['types' => [$type1_class, $type2_class]]],
			['100', ':100:', ['types' => [$type2_class, $type1_class]]],
			[$stdc, $stdc, ['types' => [$type1_class, $type2_class]]],
			[$stdc, $stdc, ['types' => [$type2_class, $type1_class]]],
			['foo', ':foo:', ['types' => [$type1_class, $type2_class]]],
			['foo', ':foo:', ['types' => [$type2_class, $type1_class]]]
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
	 * @param string $expected_error_string
	 * The expected error string.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, string $expected_error_string, array $properties = []): void
	{
		$error = Component::build(Prototype::class, $properties)->process($value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame($expected_error_string, $error->getText()->toString());
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		//initialize
		$stdc = new stdClass;
		$r = fopen(__FILE__, 'r');
		$type1_class = AnyTest_TypePrototype1::class;
		$type2_class = AnyTest_TypePrototype2::class;
		
		//return
		return [
			['foo', $type1_class::ERROR_MESSAGE, ['types' => [$type1_class]]],
			[$stdc, $type1_class::ERROR_MESSAGE, ['types' => [$type1_class]]],
			[1, $type2_class::ERROR_MESSAGE, ['types' => [$type2_class]]],
			[1.1, $type2_class::ERROR_MESSAGE, ['types' => [$type2_class]]],
			[null, $type2_class::ERROR_MESSAGE, ['types' => [$type1_class, $type2_class]]],
			[null, $type1_class::ERROR_MESSAGE, ['types' => [$type2_class, $type1_class]]],
			[false, $type2_class::ERROR_MESSAGE, ['types' => [$type1_class, $type2_class]]],
			[false, $type1_class::ERROR_MESSAGE, ['types' => [$type2_class, $type1_class]]],
			[true, $type2_class::ERROR_MESSAGE, ['types' => [$type1_class, $type2_class]]],
			[true, $type1_class::ERROR_MESSAGE, ['types' => [$type2_class, $type1_class]]],
			[[], $type2_class::ERROR_MESSAGE, ['types' => [$type1_class, $type2_class]]],
			[[], $type1_class::ERROR_MESSAGE, ['types' => [$type2_class, $type1_class]]],
			[$r, $type2_class::ERROR_MESSAGE, ['types' => [$type1_class, $type2_class]]],
			[$r, $type1_class::ERROR_MESSAGE, ['types' => [$type2_class, $type1_class]]]
		];
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
	public function testTextifierInterface(mixed $value, string $expected, array $properties = []): void
	{
		$text = Component::build(Prototype::class, $properties)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($expected, $text->toString());
	}
	
	/**
	 * Provide `Textifier` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideTextifierInterfaceData(): array
	{
		//initialize
		$stdc = new stdClass;
		$stdc_class = get_class($stdc);
		$type1_class = AnyTest_TypePrototype1::class;
		$type2_class = AnyTest_TypePrototype2::class;
		
		//return
		return [
			[false, (string)false],
			[true, (string)true],
			[1, "1"],
			[1.1, "1.1"],
			['foo', "foo"],
			[1, "1", ['types' => [$type1_class]]],
			[1.1, "1", ['types' => [$type1_class]]],
			[1, "1", ['types' => [$type1_class, $type2_class]]],
			[1, "1", ['types' => [$type2_class, $type1_class]]],
			[1.1, "1", ['types' => [$type1_class, $type2_class]]],
			[1.1, "1", ['types' => [$type2_class, $type1_class]]],
			['100', "100"],
			['100', "100", ['types' => [$type1_class, $type2_class]]],
			['100', "S(:100:)", ['types' => [$type2_class, $type1_class]]],
			[$stdc, "O({$stdc_class})", ['types' => [$type1_class, $type2_class]]],
			[$stdc, "O({$stdc_class})", ['types' => [$type2_class, $type1_class]]],
			['foo', "S(:foo:)", ['types' => [$type1_class, $type2_class]]],
			['foo', "S(:foo:)", ['types' => [$type2_class, $type1_class]]]
		];
	}
	
	/**
	 * Test `Textifier` interface (null).
	 * 
	 * @testdox Textifier interface (null)
	 * @dataProvider provideTextifierInterfaceData_Null
	 * 
	 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testTextifierInterface_Null(mixed $value, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->textify($value, no_throw: true));
	}
	
	/**
	 * Provide `Textifier` interface data (null).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideTextifierInterfaceData_Null(): array
	{
		//initialize
		$c = new AnyTest_Class;
		$type1_class = AnyTest_TypePrototype1::class;
		$type2_class = AnyTest_TypePrototype2::class;
		
		//return
		return [
			[[]],
			[new stdClass],
			[fopen(__FILE__, 'r')],
			[$c],
			[$c, ['types' => [$type1_class]]],
			[$c, ['types' => [$type2_class]]],
			[$c, ['types' => [$type1_class, $type2_class]]],
			[$c, ['types' => [$type2_class, $type1_class]]]
		];
	}
}



/** Test case dummy class. */
class AnyTest_Class {}



/** Test case dummy type prototype 1. */
class AnyTest_TypePrototype1 extends TypePrototype
{
	public const ERROR_MESSAGE = "Must be numeric.";
	
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (is_scalar($value) && is_numeric($value)) {
			$value = (int)$value;
			return null;
		}
		return Error::build(text: self::ERROR_MESSAGE);
	}
}



/** Test case dummy type prototype 2. */
class AnyTest_TypePrototype2 extends TypePrototype implements ITextifier
{
	public const ERROR_MESSAGE = "Must be a string or an object.";
	
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if (is_string($value)) {
			if ($value !== '' && $value[0] !== ':') {
				$value = ":{$value}:";
			}
			return null;
		}
		return is_object($value) ? null : Error::build(text: self::ERROR_MESSAGE);
	}
	
	public function textify(mixed $value)
	{
		if ($value instanceof AnyTest_Class) {
			return null;
		}
		return is_object($value) ? 'O(' . get_class($value) . ')' : "S({$value})";
	}
}
