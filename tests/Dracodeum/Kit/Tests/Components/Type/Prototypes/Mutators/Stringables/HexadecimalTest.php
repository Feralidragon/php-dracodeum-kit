<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Hexadecimal as Prototype;
use Dracodeum\Kit\Enumerations\TextCase as ETextCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Hexadecimal */
class HexadecimalTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * @dataProvider provideProcessData_Class
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testProcess(mixed $value): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame(strtolower($v), $value);
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
	 * @dataProvider provideProcessData_Error_Class
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testProcess_Error(mixed $value): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class)->process($value));
	}
	
	/**
	 * Test `ExplanationProducer` interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class)->getExplanation());
	}
	
	
	
	//Public static methods
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData(): array
	{
		return [
			[''],
			['9ef61b85'],
			['9EF61B85'],
			['9eF61b85'],
			['0123456789abcdefABCDEF']
		];
	}
	
	/**
	 * Provide process data (class).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Class(): array
	{
		$data = self::provideProcessData();
		foreach ($data as &$d) {
			$d[0] = new HexadecimalTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error(): array
	{
		return [
			[' '],
			['_'],
			['!'],
			['foobar'],
			['FOOBAR'],
			['FooBar'],
			['9xf61b85'],
			['9e f6 1b 85'],
			['9e-f6-1b-85'],
			['9e_f6_1b_85'],
			['9e:f6:1b:85'],
			['0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ']
		];
	}
	
	/**
	 * Provide process data (error, class).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Error_Class(): array
	{
		$data = self::provideProcessData_Error();
		foreach ($data as &$d) {
			$d[0] = new HexadecimalTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
}



/** Test case dummy class. */
class HexadecimalTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
