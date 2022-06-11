<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countable as Prototype;
use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as UCallHaltExceptions;
use Countable as ICountable;
use stdClass;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countable */
class CountableTest extends TestCase
{
	//Public methods
	/**
	 * Test `Validator` interface.
	 * 
	 * @testdox Validator interface
	 * @dataProvider provideValidatorInterfaceData
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testValidatorInterface(mixed $value): void
	{
		$this->assertNull(Component::build(CountableTest_Prototype::class)->process($value));
	}
	
	/**
	 * Provide `Validator` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValidatorInterfaceData(): array
	{
		return [
			[[]],
			[new CountableTest_Class]
		];
	}
	
	/**
	 * Test `Validator` interface (error).
	 * 
	 * @testdox Validator interface (error)
	 * @dataProvider provideValidatorInterfaceData_Error
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testValidatorInterface_Error(mixed $value): void
	{
		$this->expectException(UCallHaltExceptions\ParameterNotAllowed::class);
		Component::build(CountableTest_Prototype::class)->process($value);
	}
	
	/**
	 * Provide `Validator` interface data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideValidatorInterfaceData_Error(): array
	{
		return [
			[null],
			[false],
			[true],
			[123],
			[123.456],
			['foobar'],
			[new stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
}



/** Test case dummy prototype class. */
class CountableTest_Prototype extends Prototype
{
	public function process(mixed &$value) {}
}



/** Test case dummy class. */
class CountableTest_Class implements ICountable
{
	public function count(): int
	{
		return 0;
	}
}
