<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Exceptions\Argument;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Exceptions\Argument\Invalid as Exception;

/** @see \Dracodeum\Kit\Exceptions\Argument\Invalid */
class InvalidTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 * @dataProvider provideData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param string|null $error_message
	 * The error message to test with.
	 */
	public function test(string $name, mixed $value, ?string $error_message = null): void
	{
		//initialize
		$exception = new Exception($name, $value, error_message: $error_message);
		
		//assert
		$this->assertSame($name, $exception->name);
		$this->assertSame($value, $exception->value);
		$this->assertSame($error_message, $exception->error_message);
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
		return [
			['foo', 123],
			['bar', 'Abc', "The argument value is invalid."]
		];
	}
}
