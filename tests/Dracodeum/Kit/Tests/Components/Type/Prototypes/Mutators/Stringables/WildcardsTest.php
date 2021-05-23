<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Wildcards as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Wildcards */
class WildcardsTest extends TestCase
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
	 * 
	 * @return void
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
			['', [['']]],
			['', [['*']]],
			['a', [['a', 'b']]],
			['b', [['a', 'b']]],
			['a', [['a*', 'b*']]],
			['b', [['a*', 'b*']]],
			['a', [['*a', '*b']]],
			['b', [['*a', '*b']]],
			['a', [['*a*', '*b*']]],
			['b', [['*a*', '*b*']]],
			['foobar', [['*a*', '*b*']]],
			['foobar', [['a*', 'b*', 'f*']]],
			['foobar', [['*a', '*b', '*r']]],
			['foobar', [['a', 'b', 'foobar']]],
			['foobar', [['*A*', '*B*'], 'insensitive' => true]],
			['foobar', [['A*', 'B*', 'F*'], 'insensitive' => true]],
			['foobar', [['*A', '*B', '*R'], 'insensitive' => true]],
			['foobar', [['A', 'B', 'FooBar'], 'insensitive' => true]],
			['foobar', [['a*', 'b*'], 'negate' => true]],
			['foobar', [['*a', '*b'], 'negate' => true]],
			['foobar', [['*A*', '*B*'], 'negate' => true]],
			['foobar', [['A*', 'B*', 'F*'], 'negate' => true]],
			['foobar', [['*A', '*B', '*R'], 'negate' => true]],
			['foobar', [['A', 'B', 'FooBar'], 'negate' => true]]
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
			$d[0] = new WildcardsTest_Class($d[0]);
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
	 * 
	 * @return void
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
			['', [[' ']]],
			['', [[' *']]],
			['', [['* ']]],
			['', [[' * ']]],
			['', [[''], 'negate' => true]],
			['', [['*'], 'negate' => true]],
			['a', [['c', 'b']]],
			['b', [['a', 'c']]],
			['a', [['c*', 'b*']]],
			['b', [['a*', 'c*']]],
			['a', [['*c', '*b']]],
			['b', [['*a', '*c']]],
			['a', [['*c*', '*b*']]],
			['b', [['*a*', '*c*']]],
			['a', [['a', 'b'], 'negate' => true]],
			['b', [['a', 'b'], 'negate' => true]],
			['a', [['a*', 'b*'], 'negate' => true]],
			['b', [['a*', 'b*'], 'negate' => true]],
			['a', [['*a', '*b'], 'negate' => true]],
			['b', [['*a', '*b'], 'negate' => true]],
			['a', [['*a*', '*b*'], 'negate' => true]],
			['b', [['*a*', '*b*'], 'negate' => true]],
			['foobar', [['a', 'b']]],
			['foobar', [['a*', 'b*']]],
			['foobar', [['*a', '*b']]],
			['foobar', [['*A*', '*B*']]],
			['foobar', [['A*', 'B*', 'F*']]],
			['foobar', [['*A', '*B', '*R']]],
			['foobar', [['A', 'B', 'FooBar']]],
			['foobar', [['*a*', '*b*'], 'negate' => true]],
			['foobar', [['a*', 'b*', 'f*'], 'negate' => true]],
			['foobar', [['*a', '*b', '*r'], 'negate' => true]],
			['foobar', [['a', 'b', 'foobar'], 'negate' => true]],
			['foobar', [['*A*', '*B*'], 'insensitive' => true, 'negate' => true]],
			['foobar', [['A*', 'B*', 'F*'], 'insensitive' => true, 'negate' => true]],
			['foobar', [['*A', '*B', '*R'], 'insensitive' => true, 'negate' => true]],
			['foobar', [['A', 'B', 'FooBar'], 'insensitive' => true, 'negate' => true]]
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
			$d[0] = new WildcardsTest_Class($d[0]);
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
	 * 
	 * @return void
	 */
	public function testExplanationProducerInterface(): void
	{
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class, [['*']])->getExplanation());
	}
}



/** Test case dummy class. */
class WildcardsTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
