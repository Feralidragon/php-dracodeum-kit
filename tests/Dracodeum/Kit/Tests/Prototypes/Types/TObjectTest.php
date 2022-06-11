<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TObject as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TObject */
class TObjectTest extends TestCase
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
		$object0 = new stdClass;
		$object1 = new TObjectTest_Class1;
		$object2 = new TObjectTest_Class2;
		
		//return
		return [
			[$object0, $object0],
			[$object1, $object1],
			[$object2, $object2],
			[$object0, $object0, ['class' => stdClass::class]],
			[$object1, $object1, ['class' => TObjectTest_Class1::class]],
			[$object2, $object2, ['class' => TObjectTest_Class1::class]]
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
			[null],
			[false],
			[true],
			[1],
			[1.1],
			[''],
			[' '],
			['123'],
			['foo'],
			[[]],
			[fopen(__FILE__, 'r')],
			[stdClass::class],
			[TObjectTest_Class1::class],
			[TObjectTest_Class2::class],
			[new stdClass, ['class' => TObjectTest_Class1::class]],
			[new TObjectTest_Class1, ['class' => stdClass::class]],
			[new TObjectTest_Class1, ['class' => TObjectTest_Class2::class]]
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
	 */
	public function testTextifierInterface(mixed $value, string $expected): void
	{
		$text = Component::build(Prototype::class)->textify($value);
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
		return [
			[new stdClass, '/^object<stdClass>#\d+$/'],
			[new TObjectTest_Class1, '/^object<' . preg_quote(TObjectTest_Class1::class, '/') . '>#\d+$/'],
			[new TObjectTest_Class2, '/^object<' . preg_quote(TObjectTest_Class2::class, '/') . '>#\d+$/']
		];
	}
}



/** Test case dummy class 1. */
class TObjectTest_Class1 {}



/** Test case dummy class 2. */
class TObjectTest_Class2 extends TObjectTest_Class1 {}
