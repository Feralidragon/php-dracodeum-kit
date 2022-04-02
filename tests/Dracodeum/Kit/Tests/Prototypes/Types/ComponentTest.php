<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\Component as Prototype;
use Dracodeum\Kit\{
	Component as KitComponent,
	Prototype as KitPrototype	
};
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Traits\LazyProperties\Property;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\Component */
class ComponentTest extends TestCase
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
	 * 
	 * @return void
	 */
	public function testProcess(
		mixed $value, string $expected, array $properties, array $expected_properties = []
	): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertInstanceOf(KitComponent::class, $value);
		$this->assertSame($expected, get_class($value));
		foreach ($expected_properties as $name => $v) {
			$this->assertSame($v, $value->$name);
		}
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
		$class1 = ComponentTest_Component1::class;
		$class1a = ComponentTest_Component1_A::class;
		$class1b = ComponentTest_Component1_B::class;
		$class2 = ComponentTest_Component2::class;
		$class1_proto = ComponentTest_Prototype1::class;
		$class1_proto_a = ComponentTest_Prototype1_A::class;
		$class1_proto_b = ComponentTest_Prototype1_B::class;
		$class2_proto = ComponentTest_Prototype2::class;
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
		$builder1 = function ($prototype, array $properties): ComponentTest_Component1 {
			return new ComponentTest_Component1($prototype, $properties);
		};
		$builder1a = function ($prototype, array $properties): ComponentTest_Component1_A {
			return new ComponentTest_Component1_A($prototype, $properties);
		};
		$builder1b = function ($prototype, array $properties): ComponentTest_Component1_B {
			return new ComponentTest_Component1_B($prototype, $properties);
		};
		$builder2 = function ($prototype, array $properties): ComponentTest_Component2 {
			return new ComponentTest_Component2($prototype, $properties);
		};
		$named_builder = function (string $name, array $properties): ?KitComponent {
			return match ($name) {
				'c1' => new ComponentTest_Component1(),
				'c1a' => new ComponentTest_Component1_A(),
				'c1b' => new ComponentTest_Component1_B(),
				'c2' => new ComponentTest_Component2(),
				'c1_p1a' => new ComponentTest_Component1('p1a', $properties),
				'c1_p1b' => new ComponentTest_Component1('p1b', $properties),
				'c1a_p1a' => new ComponentTest_Component1_A('p1a', $properties),
				'c1a_p1b' => new ComponentTest_Component1_A('p1b', $properties),
				'c1b_p1a' => new ComponentTest_Component1_B('p1a', $properties),
				'c1b_p1b' => new ComponentTest_Component1_B('p1b', $properties),
				default => null
			};
		};
		
		//return
		return [
			[new $class1(), $class1, [$class1]],
			[new $class1a(), $class1a, [$class1]],
			[new $class1a(), $class1a, [$class1a]],
			[new $class1b(), $class1b, [$class1]],
			[new $class1b(), $class1b, [$class1b]],
			[new $class2(), $class2, [$class2]],
			['c1', $class1, [$class1, 'named_builder' => $named_builder]],
			['c1a', $class1a, [$class1, 'named_builder' => $named_builder]],
			['c1b', $class1b, [$class1, 'named_builder' => $named_builder]],
			['c2', $class2, [$class2, 'named_builder' => $named_builder]],
			['c1_p1a', $class1, [$class1, 'properties' => $properties1a_1, 'named_builder' => $named_builder],
				$properties1a_1e],
			['c1_p1a', $class1, [$class1, 'properties' => $properties1a_2, 'named_builder' => $named_builder],
				$properties1a_2e],
			['c1_p1a', $class1, [$class1, 'properties' => $properties1a_3, 'named_builder' => $named_builder],
				$properties1a_3e],
			['c1_p1a', $class1, [$class1, 'properties' => $properties1a_4, 'named_builder' => $named_builder],
				$properties1a_4e],
			['c1_p1b', $class1, [$class1, 'properties' => $properties1b_1, 'named_builder' => $named_builder],
				$properties1b_1e],
			['c1_p1b', $class1, [$class1, 'properties' => $properties1b_2, 'named_builder' => $named_builder],
				$properties1b_2e],
			['c1_p1b', $class1, [$class1, 'properties' => $properties1b_3, 'named_builder' => $named_builder],
				$properties1b_3e],
			['c1a_p1a', $class1a, [$class1, 'properties' => $properties1a_4, 'named_builder' => $named_builder],
				$properties1a_4e],
			['c1a_p1b', $class1a, [$class1, 'properties' => $properties1b_3, 'named_builder' => $named_builder],
				$properties1b_3e],
			['c1b_p1a', $class1b, [$class1, 'properties' => $properties1a_4, 'named_builder' => $named_builder],
				$properties1a_4e],
			['c1b_p1b', $class1b, [$class1, 'properties' => $properties1b_3, 'named_builder' => $named_builder],
				$properties1b_3e],
			[new $class1_proto(), $class1, [$class1]],
			[new $class1_proto(), $class1a, [$class1a]],
			[new $class1_proto(), $class1b, [$class1b]],
			[new $class2_proto(), $class2, [$class2]],
			[new $class1_proto_a($properties1a_1), $class1, [$class1], $properties1a_1e],
			[new $class1_proto_a($properties1a_2), $class1, [$class1], $properties1a_2e],
			[new $class1_proto_a($properties1a_3), $class1, [$class1], $properties1a_3e],
			[new $class1_proto_a($properties1a_4), $class1, [$class1], $properties1a_4e],
			[new $class1_proto_b($properties1b_1), $class1, [$class1], $properties1b_1e],
			[new $class1_proto_b($properties1b_2), $class1, [$class1], $properties1b_2e],
			[new $class1_proto_b($properties1b_3), $class1, [$class1], $properties1b_3e],
			[$class1_proto, $class1, [$class1]],
			[$class1_proto, $class1a, [$class1a]],
			[$class1_proto, $class1b, [$class1b]],
			[$class2_proto, $class2, [$class2]],
			[$class1_proto_a, $class1, [$class1, 'properties' => $properties1a_1], $properties1a_1e],
			[$class1_proto_a, $class1, [$class1, 'properties' => $properties1a_2], $properties1a_2e],
			[$class1_proto_a, $class1, [$class1, 'properties' => $properties1a_3], $properties1a_3e],
			[$class1_proto_a, $class1, [$class1, 'properties' => $properties1a_4], $properties1a_4e],
			[$class1_proto_b, $class1, [$class1, 'properties' => $properties1b_1], $properties1b_1e],
			[$class1_proto_b, $class1, [$class1, 'properties' => $properties1b_2], $properties1b_2e],
			[$class1_proto_b, $class1, [$class1, 'properties' => $properties1b_3], $properties1b_3e],
			['p1', $class1, [$class1]],
			['p1', $class1a, [$class1a]],
			['p1', $class1b, [$class1b]],
			['p1a', $class1, [$class1, 'properties' => $properties1a_1], $properties1a_1e],
			['p1a', $class1, [$class1, 'properties' => $properties1a_2], $properties1a_2e],
			['p1a', $class1, [$class1, 'properties' => $properties1a_3], $properties1a_3e],
			['p1a', $class1, [$class1, 'properties' => $properties1a_4], $properties1a_4e],
			['p1b', $class1, [$class1, 'properties' => $properties1b_1], $properties1b_1e],
			['p1b', $class1, [$class1, 'properties' => $properties1b_2], $properties1b_2e],
			['p1b', $class1, [$class1, 'properties' => $properties1b_3], $properties1b_3e],
			[$class1_proto, $class1, [$class1, 'builder' => $builder1]],
			[$class1_proto, $class1a, [$class1, 'builder' => $builder1a]],
			[$class1_proto, $class1b, [$class1, 'builder' => $builder1b]],
			[$class2_proto, $class2, [$class2, 'builder' => $builder2]],
			['p1', $class1, [$class1, 'builder' => $builder1]],
			['p1', $class1a, [$class1, 'builder' => $builder1a]],
			['p1', $class1b, [$class1, 'builder' => $builder1b]],
			['p1a', $class1, [$class1, 'builder' => $builder1, 'properties' => $properties1a_4], $properties1a_4e],
			['p1a', $class1a, [$class1, 'builder' => $builder1a, 'properties' => $properties1a_4], $properties1a_4e],
			['p1a', $class1b, [$class1, 'builder' => $builder1b, 'properties' => $properties1a_4], $properties1a_4e],
			['p1b', $class1, [$class1, 'builder' => $builder1, 'properties' => $properties1b_3], $properties1b_3e],
			['p1b', $class1a, [$class1, 'builder' => $builder1a, 'properties' => $properties1b_3], $properties1b_3e],
			['p1b', $class1b, [$class1, 'builder' => $builder1b, 'properties' => $properties1b_3], $properties1b_3e],
			['p1', $class1, [$class1, 'builder' => $builder1, 'named_builder' => $named_builder]],
			['p1', $class1a, [$class1, 'builder' => $builder1a, 'named_builder' => $named_builder]],
			['p1', $class1b, [$class1, 'builder' => $builder1b, 'named_builder' => $named_builder]],
			[$class2_proto, $class2, [$class2, 'builder' => $builder2, 'named_builder' => $named_builder]]
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
	 * @param array $properties
	 * The properties to test with.
	 * 
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
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		//initialize
		$class1 = ComponentTest_Component1::class;
		$class1a = ComponentTest_Component1_A::class;
		$class1b = ComponentTest_Component1_B::class;
		$class2 = ComponentTest_Component2::class;
		$class1_proto = ComponentTest_Prototype1::class;
		$class2_proto = ComponentTest_Prototype2::class;
		$builder1 = function ($prototype, array $properties): ComponentTest_Component1 {
			return new ComponentTest_Component1($prototype, $properties);
		};
		$builder2 = function ($prototype, array $properties): ComponentTest_Component2 {
			return new ComponentTest_Component2($prototype, $properties);
		};
		$named_builder = function (string $name, array $properties): ?KitComponent {
			return match ($name) {
				'c1' => new ComponentTest_Component1(),
				'c1a' => new ComponentTest_Component1_A(),
				'c1b' => new ComponentTest_Component1_B(),
				'c2' => new ComponentTest_Component2(),
				default => null
			};
		};
		
		//return
		return [
			[null, [$class1]],
			[false, [$class1]],
			[true, [$class1]],
			[1, [$class1]],
			[1.1, [$class1]],
			[[], [$class1]],
			[new stdClass(), [$class1]],
			[fopen(__FILE__, 'r'), [$class1]],
			[new $class1(), [$class2]],
			[new $class1(), [$class1a]],
			[new $class1(), [$class1b]],
			[new $class1a(), [$class2]],
			[new $class1b(), [$class2]],
			[new $class2(), [$class1]],
			['c1', [$class2, 'named_builder' => $named_builder]],
			['c1', [$class1a, 'named_builder' => $named_builder]],
			['c1', [$class1b, 'named_builder' => $named_builder]],
			['c1a', [$class2, 'named_builder' => $named_builder]],
			['c1b', [$class2, 'named_builder' => $named_builder]],
			['c2', [$class1, 'named_builder' => $named_builder]],
			[$class1_proto, [$class2, 'builder' => $builder1]],
			[$class1_proto, [$class1a, 'builder' => $builder1]],
			[$class1_proto, [$class1b, 'builder' => $builder1]],
			[$class2_proto, [$class1, 'builder' => $builder2]],
			[$class2_proto, [$class1a, 'builder' => $builder2]],
			[$class2_proto, [$class1b, 'builder' => $builder2]]
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
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testProcess_Strict(mixed $value, array $properties): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, ['strict' => true] + $properties)->process($v));
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
		//initialize
		$class1 = ComponentTest_Component1::class;
		$class1a = ComponentTest_Component1_A::class;
		$class1b = ComponentTest_Component1_B::class;
		$class2 = ComponentTest_Component2::class;
		
		//return
		return [
			[new $class1(), [$class1]],
			[new $class1a(), [$class1]],
			[new $class1a(), [$class1a]],
			[new $class1b(), [$class1]],
			[new $class1b(), [$class1b]],
			[new $class2(), [$class2]]
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
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testProcess_Strict_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(
			Error::class, Component::build(Prototype::class, ['strict' => true] + $properties)->process($value)
		);
	}
	
	/**
	 * Provide process data (strict, error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Strict_Error(): array
	{
		//initialize
		$class1 = ComponentTest_Component1::class;
		$class1_proto = ComponentTest_Prototype1::class;
		$named_builder = function (string $name, array $properties): ?KitComponent {
			return match ($name) {
				'c1' => new ComponentTest_Component1(),
				'c1_p1a' => new ComponentTest_Component1('p1a', $properties),
				default => null
			};
		};
		
		//return
		return [
			['c1', [$class1, 'named_builder' => $named_builder]],
			['c1_p1a', [$class1]],
			[new $class1_proto(), [$class1]],
			[$class1_proto, [$class1]],
			['p1', [$class1]]
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
	 * The expected regular expression match.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 * 
	 * @return void
	 */
	public function testTextifierInterface(mixed $value, string $expected, array $properties): void
	{
		$text = Component::build(Prototype::class, $properties)->textify($value);
		$this->assertInstanceOf(Text::class, $text);
		$this->assertMatchesRegularExpression($expected, $text->toString());
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
		$class1 = ComponentTest_Component1::class;
		$class1a = ComponentTest_Component1_A::class;
		$class1b = ComponentTest_Component1_B::class;
		$class2 = ComponentTest_Component2::class;
		
		//return
		return [
			[new $class1(), '/^component<' . preg_quote($class1, '/') . '>#\d+$/', [$class1]],
			[new $class1a(), '/^component<' . preg_quote($class1a, '/') . '>#\d+$/', [$class1]],
			[new $class1b(), '/^component<' . preg_quote($class1b, '/') . '>#\d+$/', [$class1]],
			[new $class2(), '/^component<' . preg_quote($class2, '/') . '>#\d+$/', [$class2]]
		];
	}
}



/** Test case dummy component class 1. */
class ComponentTest_Component1 extends KitComponent
{
	public static function getPrototypeBaseClass(): string
	{
		return ComponentTest_Prototype1::class;
	}
	
	protected function producePrototype(string $name, array $properties)
	{
		return match ($name) {
			'p1' => ComponentTest_Prototype1::class,
			'p1a' => ComponentTest_Prototype1_A::class,
			'p1b' => ComponentTest_Prototype1_B::class,
			default => null
		};
	}
}



/** Test case dummy component class 1-A. */
class ComponentTest_Component1_A extends ComponentTest_Component1 {}



/** Test case dummy component class 1-B. */
class ComponentTest_Component1_B extends ComponentTest_Component1 {}



/** Test case dummy prototype class 1. */
class ComponentTest_Prototype1 extends KitPrototype {}



/** Test case dummy prototype class 1-A. */
class ComponentTest_Prototype1_A extends ComponentTest_Prototype1
{
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'label' => $this->createProperty()->setAsString()->setDefaultValue(""),
			'flags' => $this->createProperty()->setAsInteger()->setDefaultValue(0x00),
			'ratio' => $this->createProperty()->setAsFloat()->setDefaultValue(1.0),
			default => null
		};
	}
}



/** Test case dummy prototype class 1-B. */
class ComponentTest_Prototype1_B extends ComponentTest_Prototype1
{
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('id');
	}
	
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'id' => $this->createProperty()->setAsString(),
			'code' => $this->createProperty()->setAsInteger()->setDefaultValue(0),
			'enabled' => $this->createProperty()->setAsBoolean()->setDefaultValue(false),
			default => null
		};
	}
}



/** Test case dummy component class 2. */
class ComponentTest_Component2 extends KitComponent
{
	public static function getPrototypeBaseClass(): string
	{
		return ComponentTest_Prototype2::class;
	}
}



/** Test case dummy prototype class 2. */
class ComponentTest_Prototype2 extends KitPrototype {}
