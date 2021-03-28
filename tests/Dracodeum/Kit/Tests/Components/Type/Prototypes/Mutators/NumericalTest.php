<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numerical as Prototype;
use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as UCallHaltExceptions;
use stdClass;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numerical */
class NumericalTest extends TestCase
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
	 * 
	 * @return void
	 */
	public function testValidatorInterface(mixed $value): void
	{
		$this->assertNull(Component::build(NumericalTest_Prototype::class)->process($value));
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
			[123],
			[123.456]
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
	 * 
	 * @return void
	 */
	public function testValidatorInterface_Error(mixed $value): void
	{
		$this->expectException(UCallHaltExceptions\ParameterNotAllowed::class);
		Component::build(NumericalTest_Prototype::class)->process($value);
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
			['foobar'],
			[[]],
			[new stdClass()],
			[fopen(__FILE__, 'r')]
		];
	}
}



/** Test case dummy prototype class. */
class NumericalTest_Prototype extends Prototype
{
	public function process(mixed &$value) {}
}
