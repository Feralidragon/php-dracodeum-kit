<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Enums\Error;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Primitives\Error;
use Dracodeum\Kit\ExceptionV2 as Exception;
use Dracodeum\Kit\Enums\Error\Type as EType;
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Exception as PhpException;
use Error as PhpError;
use Throwable;

/** @covers \Dracodeum\Kit\Enums\Error\Type */
class TypeTest extends TestCase
{
	//Public methods
	/**
	 * Test handle throwable.
	 * 
	 * @testdox HandleThrowable
	 * @dataProvider provideHandleThrowableData
	 * 
	 * @param \Throwable $throwable
	 * The throwable instance to test with.
	 * 
	 * @param mixed $expected_error_data
	 * The expected error data.
	 */
	public function testHandleThrowable(Throwable $throwable, mixed $expected_error_data = null): void
	{
		//null
		$this->assertNull(EType::NULL->handleThrowable($throwable));
		
		//error
		$error = EType::ERROR->handleThrowable($throwable);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame($throwable::class, $error->getName());
		$this->assertSame($throwable->getMessage(), $error->getText()->toString());
		$this->assertSame($throwable, $error->getThrowable());
		$this->assertNull($error->getData());
		$this->assertNull($error->getData(EInfoLevel::ENDUSER));
		$this->assertNull($error->getData(EInfoLevel::TECHNICAL));
		$this->assertSame($expected_error_data, $error->getData(EInfoLevel::INTERNAL));
		
		//throwable
		$this->expectException($throwable::class);
		try {
			EType::THROWABLE->handleThrowable($throwable);
		} catch (Throwable $t) {
			$this->assertSame($throwable, $t);
			throw $t;
		}
	}
	
	
	
	//Public static methods
	/**
	 * Provide handle throwable data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideHandleThrowableData(): array
	{
		return [
			[new PhpError("An error occurred.")],
			[new PhpException("An exception occurred.")],
			[new TypeTest_Exception('Foo'), ['name' => 'Foo', 'number' => 123]]
		];
	}
}



/** Test case dummy exception. */
class TypeTest_Exception extends Exception
{
	public string $name;
	public int $number = 123;
	
	protected function produceText()
	{
		return "An error occurred for {{name}} with {{number}}.";
	}
}
