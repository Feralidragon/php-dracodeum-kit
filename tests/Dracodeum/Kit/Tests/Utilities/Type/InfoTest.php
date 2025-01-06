<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Utilities\Type;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Utilities\Type\Info;
use Dracodeum\Kit\Utilities\Type\Info\Enums\Kind as EKind;
use Dracodeum\Kit\Exceptions\Argument\Invalid as InvalidArgumentException;

/** @covers \Dracodeum\Kit\Utilities\Type\Info */
class InfoTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 */
	public function test(): void
	{
		//initialize
		$kind = EKind::UNION;
		$name = 'array';
		$names = ['int', 'string', 'stdClass'];
		$flags = '*?';
		$parameters = ['foo' => 'bar', 'k' => 123];
		$info = new Info($kind, $name, $names, $flags, $parameters);
		
		//assert
		$this->assertSame($kind, $info->kind);
		$this->assertSame($name, $info->name);
		$this->assertSame($names, $info->names);
		$this->assertSame($flags, $info->flags);
		$this->assertSame($parameters, $info->parameters);
	}
	
	/**
	 * Test expecting an `Argument\Invalid` exception to be thrown.
	 * 
	 * @testdox Test Argument\Invalid exception
	 * @dataProvider provideData_Exception_InvalidArgument
	 * 
	 * @param array $names
	 * The names to test with.
	 */
	public function test_Exception_InvalidArgument(array $names): void
	{
		$this->expectException(InvalidArgumentException::class);
		try {
			new Info(EKind::GENERIC, names: $names);
		} catch (InvalidArgumentException $exception) {
			$this->assertSame('names', $exception->name);
			$this->assertSame($names, $exception->value);
			$this->assertNotNull($exception->error_message);
			throw $exception;
		}
	}
	
	
	
	//Public static methods
	/**
	 * Provide data for an `Argument\Invalid` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideData_Exception_InvalidArgument(): array
	{
		return [
			[[1 => 'foo']],
			[['a' => 'foo']]
		];
	}
}
