<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\ExceptionV2 as Exception;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Throwable;

/** @covers \Dracodeum\Kit\ExceptionV2 */
class ExceptionTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * @dataProvider provideData
	 * 
	 * @param \Dracodeum\Kit\Tests\ExceptionTest_Class $exception
	 * The exception instance to test with.
	 * 
	 * @param string $name
	 * The expected name.
	 * 
	 * @param int $number
	 * The expected number.
	 * 
	 * @param int $code
	 * The expected code.
	 * 
	 * @param \Throwable|null $previous
	 * The expected previous throwable instance.
	 * 
	 * @param string $message
	 * The expected message.
	 */
	public function test(
		ExceptionTest_Class $exception, string $name, int $number, int $code, ?Throwable $previous, string $message
	): void
	{
		$this->assertSame($name, $exception->name);
		$this->assertSame($number, $exception->number);
		$this->assertSame($code, $exception->getCode());
		$this->assertSame($previous, $exception->getPrevious());
		$this->assertSame($message, $exception->getMessage());
	}
	
	/**
	 * Test text.
	 * 
	 * @testdox Text
	 */
	public function testText(): void
	{
		//initialize
		$text = (new ExceptionTest_Class('Foo'))->getText();
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame('An error occurred for "Foo" with 123.', $text->toString());
	}
	
	/**
	 * Test error.
	 * 
	 * @testdox Error
	 */
	public function testError(): void
	{
		//initialize
		$exception = new ExceptionTest_Class('Foo');
		$error = $exception->toError();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame(ExceptionTest_Class::class, $error->getName());
		$this->assertSame('An error occurred for "Foo" with 123.', $error->getText()->toString());
		$this->assertSame($exception, $error->getThrowable());
		$this->assertNull($error->getData());
		$this->assertNull($error->getData(EInfoLevel::ENDUSER));
		$this->assertNull($error->getData(EInfoLevel::TECHNICAL));
		$this->assertSame(['name' => 'Foo', 'number' => 123], $error->getData(EInfoLevel::INTERNAL));
	}
	
	
	
	//Public static methods
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideData(): array
	{
		//initialize
		$previous = new \Exception;
		
		//return
		return [
			[new ExceptionTest_Class('Foo'), 'Foo', 123, 0, null, 'An error occurred for "Foo" with 123.'],
			[new ExceptionTest_Class(300), '300', 123, 0, null, 'An error occurred for "300" with 123.'],
			[new ExceptionTest_Class('Foo', number: 456), 'Foo', 456, 0, null, 'An error occurred for "Foo" with 456.'],
			[new ExceptionTest_Class(300, number: '456'), '300', 456, 0, null, 'An error occurred for "300" with 456.'],
			[
				new ExceptionTest_Class('Foo', code: 250), 'Foo', 123, 250, null,
				'An error occurred for "Foo" with 123.'
			], [
				new ExceptionTest_Class('Foo', previous: $previous), 'Foo', 123, 0, $previous,
				'An error occurred for "Foo" with 123.'
			], [
				new ExceptionTest_Class(300, number: '456', code: 250, previous: $previous), '300', 456, 250, $previous,
				'An error occurred for "300" with 456.'
			]
		];
	}
}



/** Test case dummy class. */
class ExceptionTest_Class extends Exception
{
	public string $name;
	public int $number = 123;
	
	protected function produceText()
	{
		return "An error occurred for {{name}} with {{number}}.";
	}
}
