<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\ExceptionV2 as Exception;

/** @see \Dracodeum\Kit\ExceptionV2 */
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
	 * @param string $message
	 * The expected message.
	 */
	public function test(ExceptionTest_Class $exception, string $name, int $number, string $message): void
	{
		$this->assertSame($name, $exception->name);
		$this->assertSame($number, $exception->number);
		$this->assertSame($message, $exception->getMessage());
	}
	
	/**
	 * Provide data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideData(): array
	{
		return [
			[new ExceptionTest_Class('Foo'), 'Foo', 123, 'An error occurred for "Foo" with 123.'],
			[new ExceptionTest_Class(300), '300', 123, 'An error occurred for "300" with 123.'],
			[new ExceptionTest_Class('Foo', number: 456), 'Foo', 456, 'An error occurred for "Foo" with 456.'],
			[new ExceptionTest_Class(300, number: '456'), '300', 456, 'An error occurred for "300" with 456.']
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
