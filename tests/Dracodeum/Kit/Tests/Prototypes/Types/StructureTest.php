<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Structure as Prototype;
use Dracodeum\Kit\Structure as KStructure;
use Dracodeum\Kit\Interfaces\Arrayable as IArrayable;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Structure */
class StructureTest extends TestCase
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
	 * @param string $expected
	 * The expected processed value class.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @param array $expected_properties
	 * The expected properties from the processed value.
	 */
	public function testProcess(
		mixed $value, string $expected, array $properties, array $expected_properties = []
	): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertInstanceOf(KStructure::class, $value);
		$this->assertSame($expected, get_class($value));
		foreach ($expected_properties as $name => $v) {
			$this->assertSame($v, $value->$name);
		}
	}
	
	/**
	 * Test process (clone).
	 * 
	 * @testdox Process (clone)
	 */
	public function testProcess_Clone(): void
	{
		//initialize
		$class1 = StructureTest_Class1::class;
		$class1a = StructureTest_Class1_A::class;
		$properties1a = ['label' => "Bar", 'flags' => 0xbd47, 'ratio' => 4.765];
		
		//assert
		foreach ([false, true] as $strict) {
			//keep
			$value = $v = new $class1a($properties1a);
			$this->assertNull(Component::build(Prototype::class, [$class1, 'strict' => $strict])->process($v));
			$this->assertInstanceOf($class1a, $v);
			$this->assertSame($value, $v);
			foreach ($properties1a as $p_name => $p_value) {
				$this->assertSame($p_value, $v->$p_name);
			}
			
			//clone
			$value = $v = new $class1a($properties1a);
			$this->assertNull(
				Component::build(Prototype::class, [$class1, 'clone' => true, 'strict' => $strict])->process($v)
			);
			$this->assertInstanceOf($class1a, $v);
			$this->assertNotSame($value, $v);
			foreach ($properties1a as $p_name => $p_value) {
				$this->assertSame($p_value, $v->$p_name);
			}
		}
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
	 * The expected regular expression match.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testTextifierInterface(mixed $value, string $expected, array $properties): void
	{
		$text = Component::build(Prototype::class, $properties)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertMatchesRegularExpression($expected, $text->toString());
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
		$class1 = StructureTest_Class1::class;
		$class1a = StructureTest_Class1_A::class;
		$class1b = StructureTest_Class1_B::class;
		$class2 = StructureTest_Class2::class;
		$class2a = StructureTest_Class2_A::class;
		$class_array = StructureTest_ArrayClass::class;
		$properties1a_1 = [];
		$properties1a_1e = ['label' => "", 'flags' => 0x00, 'ratio' => 1.0];
		$properties1a_2 = ['label' => "Foo Bar"];
		$properties1a_2e = ['label' => "Foo Bar", 'flags' => 0x00, 'ratio' => 1.0];
		$properties1a_3 = ['label' => "Foo", 'flags' => 0xf4];
		$properties1a_3e = ['label' => "Foo", 'flags' => 0xf4, 'ratio' => 1.0];
		$properties1a_4 = $properties1a_4e = ['label' => "Bar", 'flags' => 0xbd47, 'ratio' => 4.765];
		$properties1b_1 = ['XJ8K22'];
		$properties1b_1e = ['id' => 'XJ8K22', 'code' => 0, 'enabled' => false];
		$properties1b_2 = ['N33HTZ', 'code' => 1260];
		$properties1b_2e = ['id' => 'N33HTZ', 'code' => 1260, 'enabled' => false];
		$properties1b_3 = $properties1b_3e = ['id' => 'E0QWMV', 'code' => 5189, 'enabled' => true];
		$properties2a_1 = [];
		$properties2a_1e = ['label' => "-", 'ratio' => 2.5];
		$properties2a_2 = ['label' => "BarFoo"];
		$properties2a_2e = ['label' => "BarFoo", 'ratio' => 2.5];
		$properties2a_3 = $properties2a_3e = ['label' => "Fooar", 'ratio' => 3.75];
		$builder1 = function (array $properties): StructureTest_Class1 {
			return new StructureTest_Class1($properties);
		};
		$builder1a = function (array $properties): StructureTest_Class1_A {
			return new StructureTest_Class1_A($properties);
		};
		$builder1b = function (array $properties): StructureTest_Class1_B {
			return new StructureTest_Class1_B($properties);
		};
		$builder2 = function (array $properties): StructureTest_Class2 {
			return new StructureTest_Class2($properties);
		};
		$builder2a = function (array $properties): StructureTest_Class2_A {
			return new StructureTest_Class2_A($properties);
		};
		
		//return
		return [
			[null, $class1, [$class1]],
			[null, $class2, [$class2]],
			[null, $class1a, [$class1a], $properties1a_1e],
			[null, $class2a, [$class2a], $properties2a_1e],
			[new $class1, $class1, [$class1]],
			[new $class2, $class2, [$class2]],
			[new $class1a($properties1a_1), $class1a, [$class1], $properties1a_1e],
			[new $class1a($properties1a_1), $class1a, [$class1a], $properties1a_1e],
			[new $class1a($properties1a_2), $class1a, [$class1], $properties1a_2e],
			[new $class1a($properties1a_2), $class1a, [$class1a], $properties1a_2e],
			[new $class1a($properties1a_3), $class1a, [$class1], $properties1a_3e],
			[new $class1a($properties1a_3), $class1a, [$class1a], $properties1a_3e],
			[new $class1a($properties1a_4), $class1a, [$class1], $properties1a_4e],
			[new $class1a($properties1a_4), $class1a, [$class1a], $properties1a_4e],
			[new $class1b($properties1b_1), $class1b, [$class1], $properties1b_1e],
			[new $class1b($properties1b_1), $class1b, [$class1b], $properties1b_1e],
			[new $class1b($properties1b_2), $class1b, [$class1], $properties1b_2e],
			[new $class1b($properties1b_2), $class1b, [$class1b], $properties1b_2e],
			[new $class1b($properties1b_3), $class1b, [$class1], $properties1b_3e],
			[new $class1b($properties1b_3), $class1b, [$class1b], $properties1b_3e],
			[new $class2a($properties2a_1), $class1a, [$class1a], $properties2a_1e],
			[new $class2a($properties2a_2), $class1a, [$class1a], $properties2a_2e],
			[new $class2a($properties2a_3), $class1a, [$class1a], $properties2a_3e],
			[$properties1a_1, $class1a, [$class1a], $properties1a_1e],
			[$properties1a_2, $class1a, [$class1a], $properties1a_2e],
			[$properties1a_3, $class1a, [$class1a], $properties1a_3e],
			[$properties1a_4, $class1a, [$class1a], $properties1a_4e],
			[$properties1b_1, $class1b, [$class1b], $properties1b_1e],
			[$properties1b_2, $class1b, [$class1b], $properties1b_2e],
			[$properties1b_3, $class1b, [$class1b], $properties1b_3e],
			[new $class_array($properties1a_1), $class1a, [$class1a], $properties1a_1e],
			[new $class_array($properties1a_2), $class1a, [$class1a], $properties1a_2e],
			[new $class_array($properties1a_3), $class1a, [$class1a], $properties1a_3e],
			[new $class_array($properties1a_4), $class1a, [$class1a], $properties1a_4e],
			[new $class_array($properties1b_1), $class1b, [$class1b], $properties1b_1e],
			[new $class_array($properties1b_2), $class1b, [$class1b], $properties1b_2e],
			[new $class_array($properties1b_3), $class1b, [$class1b], $properties1b_3e],
			[null, $class1, [$class1, 'builder' => $builder1]],
			[null, $class1a, [$class1, 'builder' => $builder1a]],
			[null, $class2, [$class2, 'builder' => $builder2]],
			[null, $class2a, [$class2, 'builder' => $builder2a]],
			[$properties1a_4, $class1a, [$class1, 'builder' => $builder1a], $properties1a_4e],
			[$properties1b_2, $class1b, [$class1, 'builder' => $builder1b], $properties1b_2e],
			[$properties1b_3, $class1b, [$class1, 'builder' => $builder1b], $properties1b_3e],
			[$properties2a_3, $class2a, [$class2, 'builder' => $builder2a], $properties2a_3e]
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
		$class1 = StructureTest_Class1::class;
		$class1a = StructureTest_Class1_A::class;
		$class1b = StructureTest_Class1_B::class;
		$class2 = StructureTest_Class2::class;
		$builder1 = function (array $properties): StructureTest_Class1 {
			return new StructureTest_Class1($properties);
		};
		$builder2 = function (array $properties): StructureTest_Class2 {
			return new StructureTest_Class2($properties);
		};
		
		//return
		return [
			[false, [$class1]],
			[true, [$class1]],
			[1, [$class1]],
			[1.1, [$class1]],
			['', [$class1]],
			[' ', [$class1]],
			['123', [$class1]],
			['foo', [$class1]],
			[new stdClass, [$class1]],
			[fopen(__FILE__, 'r'), [$class1]],
			[null, [$class1, 'builder' => $builder2]],
			[null, [$class1a, 'builder' => $builder1]],
			[null, [$class1b, 'builder' => $builder1]],
			[null, [$class2, 'builder' => $builder1]]
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
		$class1 = StructureTest_Class1::class;
		$class2 = StructureTest_Class2::class;
		
		//return
		return [
			[new $class1, [$class1]],
			[new $class2, [$class2]]
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
		//initialize
		$class1 = StructureTest_Class1::class;
		$class_array = StructureTest_ArrayClass::class;
		
		//return
		return [
			[null, [$class1]],
			[[], [$class1]],
			[new $class_array([]), [$class1]]
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
		$class1 = StructureTest_Class1::class;
		$class1a = StructureTest_Class1_A::class;
		$class2 = StructureTest_Class2::class;
		$class2a = StructureTest_Class2_A::class;
		
		//return
		return [
			[new $class1, '/^structure<' . preg_quote($class1, '/') . '>#\d+$/', [$class1]],
			[new $class1a, '/^structure<' . preg_quote($class1a, '/') . '>#\d+$/', [$class1]],
			[new $class2, '/^structure<' . preg_quote($class2, '/') . '>#\d+$/', [$class2]],
			[new $class2a, '/^structure<' . preg_quote($class2a, '/') . '>#\d+$/', [$class2]]
		];
	}
}



/** Test case dummy class 1. */
class StructureTest_Class1 extends KStructure
{
	protected function loadProperties(): void {}
}



/** Test case dummy class 1-A. */
class StructureTest_Class1_A extends StructureTest_Class1
{
	protected function loadProperties(): void
	{
		$this->addProperty('label')->setAsString()->setDefaultValue("");
		$this->addProperty('flags')->setAsInteger()->setDefaultValue(0x00);
		$this->addProperty('ratio')->setAsFloat()->setDefaultValue(1.0);
	}
}



/** Test case dummy class 1-B. */
class StructureTest_Class1_B extends StructureTest_Class1
{
	protected function loadProperties(): void
	{
		$this->addProperty('id')->setAsString();
		$this->addProperty('code')->setAsInteger()->setDefaultValue(0);
		$this->addProperty('enabled')->setAsBoolean()->setDefaultValue(false);
	}
}



/** Test case dummy class 2. */
class StructureTest_Class2 extends KStructure
{
	protected function loadProperties(): void {}
}



/** Test case dummy class 2-A. */
class StructureTest_Class2_A extends StructureTest_Class2
{
	protected function loadProperties(): void
	{
		$this->addProperty('label')->setAsString()->setDefaultValue("-");
		$this->addProperty('ratio')->setAsFloat()->setDefaultValue(2.5);
	}
}



/** Test case dummy array class. */
class StructureTest_ArrayClass implements IArrayable
{
	public function __construct(private array $properties) {}
	
	public function toArray(): array
	{
		return $this->properties;
	}
}
