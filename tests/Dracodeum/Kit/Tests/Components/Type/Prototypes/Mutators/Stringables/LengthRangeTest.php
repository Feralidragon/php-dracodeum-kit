<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\LengthRange as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\LengthRange */
class LengthRangeTest extends TestCase
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
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess(mixed $value, array $properties): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class, $properties)->process($value));
		$this->assertSame($v, $value);
	}
	
	/**
	 * Provide process data.
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData(): array
	{
		return [
			['a', [0, 2]],
			['  ', [1, 5]],
			['foo', [2, 3]],
			['Foo Bar', [2, 10]],
			["foo\u{2003}b\u{01d4b6}r", [10, 14]],
			["foo\u{2003}b\u{01d4b6}r", [4, 9, 'unicode' => true]]
		];
	}
	
	/**
	 * Provide process data (class).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Class(): array
	{
		$data = $this->provideProcessData();
		foreach ($data as &$d) {
			$d[0] = new LengthRangeTest_Class($d[0]);
		}
		unset($d);
		return $data;
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
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Error(mixed $value, array $properties): void
	{
		$this->assertInstanceOf(Error::class, Component::build(Prototype::class, $properties)->process($value));
	}
	
	/**
	 * Provide process data (error).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error(): array
	{
		return [
			['', [1, 2]],
			['  ', [4, 6]],
			['foo', [0, 2]],
			['foo', [4, 6]],
			['Foo Bar', [1, 6]],
			['Foo Bar', [8, 15]],
			["foo\u{2003}b\u{01d4b6}r", [2, 11]],
			["foo\u{2003}b\u{01d4b6}r", [13, 20]],
			["foo\u{2003}b\u{01d4b6}r", [1, 6, 'unicode' => true]],
			["foo\u{2003}b\u{01d4b6}r", [8, 12, 'unicode' => true]]
		];
	}
	
	/**
	 * Provide process data (error, class).
	 * 
	 * @return array
	 * The data.
	 */
	public function provideProcessData_Error_Class(): array
	{
		$data = $this->provideProcessData_Error();
		foreach ($data as &$d) {
			$d[0] = new LengthRangeTest_Class($d[0]);
		}
		unset($d);
		return $data;
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
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [5, 10])->getExplanation());
	}
}



/** Test case dummy class. */
class LengthRangeTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
