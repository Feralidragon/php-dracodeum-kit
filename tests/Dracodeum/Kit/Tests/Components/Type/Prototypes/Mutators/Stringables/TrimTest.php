<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Trim as Prototype;

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Trim */
class TrimTest extends TestCase
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
	 * 
	 * @param mixed $expected
	 * The expected value.
	 */
	public function testProcess(mixed $value, mixed $expected): void
	{
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($expected, $value);
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
			[' ', ''],
			['a', 'a'],
			[' a', 'a'],
			['a ', 'a'],
			[' a ', 'a'],
			['foo bar', 'foo bar'],
			[' foo bar', 'foo bar'],
			['foo bar ', 'foo bar'],
			[' foo bar ', 'foo bar'],
			["  foo\nbar\t\n", "foo\nbar"],
			["\n\tfoo\tbar\n", "foo\tbar"]
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
			$d[0] = new TrimTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
}



/** Test case dummy class. */
class TrimTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
