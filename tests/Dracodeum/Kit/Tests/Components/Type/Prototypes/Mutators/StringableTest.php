<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable as Prototype;
use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as UCallHaltExceptions;
use stdClass;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringable */
class StringableTest extends TestCase
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
		$this->assertNull(Component::build(StringableTest_Prototype::class)->process($value));
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
		Component::build(StringableTest_Prototype::class)->process($value);
	}
	
	
	
	//Public static methods
	/**
	 * Provide `Validator` interface data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideValidatorInterfaceData(): array
	{
		return [
			['foobar'],
			[new StringableTest_Class]
		];
	}
	
	/**
	 * Provide `Validator` interface data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideValidatorInterfaceData_Error(): array
	{
		return [
			[null],
			[false],
			[true],
			[123],
			[123.456],
			[[]],
			[new stdClass],
			[fopen(__FILE__, 'r')]
		];
	}
}



/** Test case dummy prototype class. */
class StringableTest_Prototype extends Prototype
{
	public function process(mixed &$value) {}
}



/** Test case dummy class. */
class StringableTest_Class
{
	public function __toString(): string
	{
		return '';
	}
}
