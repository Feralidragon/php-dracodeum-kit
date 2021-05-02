<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TInterface as Prototype;
use Dracodeum\Kit\Primitives\Error;
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
	 * 
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
}



/** Test case dummy interface. */
interface TInterfaceTest_Interface {}



/** Test case dummy class. */
class TInterfaceTest_Class {}
