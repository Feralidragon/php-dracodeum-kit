<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\ToUppercase as Prototype;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\ToUppercase */
class ToUppercaseTest extends TestCase
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
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess(mixed $value, mixed $expected, array $properties = []): void
	{
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
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
			['a', 'A'],
			['A', 'A'],
			['foobar', 'FOOBAR'],
			['FOOBAR', 'FOOBAR'],
			['FooBar', 'FOOBAR'],
			['Foo123', 'FOO123'],
			["\u{0393}", "\u{0393}"],
			["\u{03b3}", "\u{03b3}"],
			["\u{0393}", "\u{0393}", ['unicode' => true]],
			["\u{03b3}", "\u{0393}", ['unicode' => true]],
			["f\u{03a9}\u{03c9} B\u{03b3}\u{0394}", "F\u{03a9}\u{03c9} B\u{03b3}\u{0394}"],
			["f\u{03a9}\u{03c9} B\u{03b3}\u{0394}", "F\u{03a9}\u{03a9} B\u{0393}\u{0394}", ['unicode' => true]]
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
			$d[0] = new ToUppercaseTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
}



/** Test case dummy class. */
class ToUppercaseTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
