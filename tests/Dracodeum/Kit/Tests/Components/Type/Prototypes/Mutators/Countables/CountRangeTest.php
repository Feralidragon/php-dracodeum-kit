<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Countables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\CountRange as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Countable as ICountable;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\CountRange */
class CountRangeTest extends TestCase
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
			[[], [0, 1]],
			[[123], [0, 1]],
			[[123], [1, 2]],
			[[123, 'foo'], [1, 2]],
			[[123, 'foo'], [2, 3]],
			[[123, 'foo'], [1, 4]],
			[[123, 'foo', 'bar'], [1, 3]],
			[[123, 'foo', 'bar'], [3, 5]],
			[[123, 'foo', 'bar'], [0, 7]],
			[[1, 'foo', 2, 'bar', 3], [0, 5]],
			[[1, 'foo', 2, 'bar', 3], [5, 10]],
			[[1, 'foo', 2, 'bar', 3], [1, 8]]
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
			$d[0] = new CountRangeTest_Class($d[0]);
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
			[[], [1, 2]],
			[[123], [2, 3]],
			[[123, 'foo'], [0, 1]],
			[[123, 'foo'], [3, 4]],
			[[123, 'foo', 'bar'], [0, 2]],
			[[123, 'foo', 'bar'], [1, 2]],
			[[123, 'foo', 'bar'], [4, 5]],
			[[123, 'foo', 'bar'], [6, 9]],
			[[1, 'foo', 2, 'bar', 3], [1, 3]],
			[[1, 'foo', 2, 'bar', 3], [0, 4]],
			[[1, 'foo', 2, 'bar', 3], [6, 8]],
			[[1, 'foo', 2, 'bar', 3], [7, 10]]
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
			$d[0] = new CountRangeTest_Class($d[0]);
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
class CountRangeTest_Class implements ICountable
{
	public function __construct(private array $array) {}
	
	public function count(): int
	{
		return count($this->array);
	}
}
