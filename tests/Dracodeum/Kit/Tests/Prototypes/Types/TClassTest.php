<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Prototypes\Types;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Types\TClass as Prototype;
use Dracodeum\Kit\Primitives\Error;
use stdClass;

/** @see \Dracodeum\Kit\Prototypes\Types\TClass */
class TClassTest extends TestCase
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
	 * 
	 * @return void
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
		return [
			['stdClass', stdClass::class],
			['\stdClass', stdClass::class],
			[stdClass::class, stdClass::class],
			[new stdClass(), stdClass::class],
			['Dracodeum\Kit\Tests\Prototypes\Types\TClassTest_Class1', TClassTest_Class1::class],
			['\Dracodeum\Kit\Tests\Prototypes\Types\TClassTest_Class1', TClassTest_Class1::class],
			[TClassTest_Class1::class, TClassTest_Class1::class],
			[new TClassTest_Class1(), TClassTest_Class1::class],
			[stdClass::class, stdClass::class, ['class' => stdClass::class]],
			[new stdClass(), stdClass::class, ['class' => stdClass::class]],
			[TClassTest_Class1::class, TClassTest_Class1::class, ['class' => TClassTest_Class1::class]],
			[new TClassTest_Class1(), TClassTest_Class1::class, ['class' => TClassTest_Class1::class]],
			[TClassTest_Class2::class, TClassTest_Class2::class, ['class' => TClassTest_Class1::class]],
			[new TClassTest_Class2(), TClassTest_Class2::class, ['class' => TClassTest_Class1::class]]
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
	public function testProcess_Error(mixed $value, array $properties = []): void
	{
		$v = $value;
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($v));
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
			['_stdClass'],
			['#stdClass'],
			[' stdClass '],
			['\\\stdClass'],
			[\stdClass1::class],
			['_Dracodeum\Kit\Tests\Prototypes\Types\TClassTest_Class1'],
			['\\\Dracodeum\Kit\Tests\Prototypes\Types\TClassTest_Class1'],
			[stdClass::class, ['class' => TClassTest_Class1::class]],
			[new stdClass(), ['class' => TClassTest_Class1::class]],
			[TClassTest_Class1::class, ['class' => stdClass::class]],
			[new TClassTest_Class1(), ['class' => stdClass::class]],
			[TClassTest_Class1::class, ['class' => TClassTest_Class2::class]],
			[new TClassTest_Class1(), ['class' => TClassTest_Class2::class]]
		];
	}
}



/** Test case dummy class 1. */
class TClassTest_Class1 {}



/** Test case dummy class 2. */
class TClassTest_Class2 extends TClassTest_Class1 {}
