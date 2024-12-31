<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutator\Interfaces;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator as IValidator;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as UCallHaltExceptions;

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\Validator */
class ValidatorTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 */
	public function test(): void
	{
		//build
		$component = Component::build(ValidatorTest_Prototype::class);
		
		//value (success)
		$value = 100;
		$this->assertNull($component->process($value));
		
		//value (exception)
		$value = 105;
		$this->expectException(UCallHaltExceptions\ParameterNotAllowed::class);
		$component->process($value);
	}
}



/** Test case dummy prototype class. */
class ValidatorTest_Prototype extends Prototype implements IValidator
{
	public function process(mixed &$value) {}
	
	public function validate(mixed $value): bool
	{
		return $value !== 105;
	}
}
