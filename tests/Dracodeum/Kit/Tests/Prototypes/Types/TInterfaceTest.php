<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TInterface as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Stringable as IStringable;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TInterface */
class TInterfaceTest extends TestCase
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
			['Stringable', IStringable::class],
			['\Stringable', IStringable::class],
			[IStringable::class, IStringable::class],
			['Dracodeum\Kit\Tests\Prototypes\Types\TInterfaceTest_Interface', TInterfaceTest_Interface::class],
			['\Dracodeum\Kit\Tests\Prototypes\Types\TInterfaceTest_Interface', TInterfaceTest_Interface::class],
			[TInterfaceTest_Interface::class, TInterfaceTest_Interface::class]
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
			[1],
			[1.1],
			[''],
			[' '],
			['123'],
			['foo'],
			[[]],
			[new stdClass],
			[fopen(__FILE__, 'r')],
			[stdClass::class],
			[TInterfaceTest_Class::class],
			['_Stringable'],
			['#Stringable'],
			[' Stringable '],
			['\\\Stringable'],
			[\Stringable1::class],
			['_Dracodeum\Kit\Tests\Prototypes\Types\TInterfaceTest_Interface'],
			['\\\Dracodeum\Kit\Tests\Prototypes\Types\TInterfaceTest_Interface']
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
	 */
	public function testTextifierInterface(mixed $value, string $expected): void
	{
		$text = Component::build(Prototype::class)->textify($value);
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
		$interface = TInterfaceTest_Interface::class;
		
		//return
		return [
			['Stringable', "interface<Stringable>"],
			[$interface, "interface<{$interface}>"]
		];
	}
}



/** Test case dummy interface. */
interface TInterfaceTest_Interface {}



/** Test case dummy class. */
class TInterfaceTest_Class {}
