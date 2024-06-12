<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Countables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\MaxCount as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Countable as ICountable;

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Countables\MaxCount */
class MaxCountTest extends TestCase
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
	 * Test `ExplanationProducer` interface.
	 * 
	 * @testdox ExplanationProducer interface
	 * 
	 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [10])->getExplanation());
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
			[[], [0]],
			[[], [1]],
			[[123], [1]],
			[[123], [2]],
			[[123, 'foo'], [2]],
			[[123, 'foo'], [3]],
			[[123, 'foo'], [4]],
			[[123, 'foo', 'bar'], [3]],
			[[123, 'foo', 'bar'], [5]],
			[[123, 'foo', 'bar'], [7]],
			[[1, 'foo', 2, 'bar', 3], [5]],
			[[1, 'foo', 2, 'bar', 3], [8]],
			[[1, 'foo', 2, 'bar', 3], [10]]
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
			$d[0] = new MaxCountTest_Class($d[0]);
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
			[[123], [0]],
			[[123, 'foo'], [0]],
			[[123, 'foo'], [1]],
			[[123, 'foo', 'bar'], [0]],
			[[123, 'foo', 'bar'], [1]],
			[[123, 'foo', 'bar'], [2]],
			[[1, 'foo', 2, 'bar', 3], [1]],
			[[1, 'foo', 2, 'bar', 3], [3]],
			[[1, 'foo', 2, 'bar', 3], [4]]
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
			$d[0] = new MaxCountTest_Class($d[0]);
		}
		unset($d);
		return $data;
	}
}



/** Test case dummy class. */
class MaxCountTest_Class implements ICountable
{
	public function __construct(private array $array) {}
	
	public function count(): int
	{
		return count($this->array);
	}
}
