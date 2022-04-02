<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Identifier as Prototype;
use Dracodeum\Kit\Enumerations\TextCase as ETextCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Identifier */
class IdentifierTest extends TestCase
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
	public function testProcess(mixed $value, array $properties = []): void
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
			['_'],
			['a'],
			['A'],
			['foobar'],
			['FOOBAR'],
			['FooBar'],
			['foo_bar'],
			['foobar123'],
			['_FooBar123'],
			['foobar', ['case' => ETextCase::LOWER]],
			['foobar123', ['case' => ETextCase::LOWER]],
			['foobar_123', ['case' => ETextCase::LOWER]],
			['_foobar___123_', ['case' => ETextCase::LOWER]],
			['FOOBAR', ['case' => ETextCase::UPPER]],
			['FOOBAR123', ['case' => ETextCase::UPPER]],
			['FOOBAR_123', ['case' => ETextCase::UPPER]],
			['_FOOBAR___123_', ['case' => ETextCase::UPPER]],
			['_._', ['extended' => true]],
			['foo.bar', ['extended' => true]],
			['foo.BAR', ['extended' => true]],
			['foo.bar123', ['extended' => true]],
			['F0o.B_r123', ['extended' => true]],
			['a.__.foobar123', ['extended' => true]],
			['F00_123.Bar567.__4__', ['extended' => true]],
			['f00_123.bar567.__4__', ['case' => ETextCase::LOWER, 'extended' => true]],
			['F00_123.BAR567.__4__', ['case' => ETextCase::UPPER, 'extended' => true]]
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
			$d[0] = new IdentifierTest_Class($d[0]);
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
	public function testProcess_Error(mixed $value, array $properties = []): void
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
			[''],
			[' '],
			['!'],
			['1'],
			['123'],
			['123foobar'],
			['123FOOBAR'],
			['123FooBar'],
			['.'],
			['..'],
			['_._'],
			['foo bar'],
			['foo.bar'],
			['foo.BAR'],
			['foo.bar123'],
			['FooBar', ['case' => ETextCase::LOWER]],
			['FooBar', ['case' => ETextCase::UPPER]],
			['FooBar123', ['case' => ETextCase::LOWER]],
			['FooBar123', ['case' => ETextCase::UPPER]],
			['FooBar_123', ['case' => ETextCase::LOWER]],
			['FooBar_123', ['case' => ETextCase::UPPER]],
			['', ['extended' => true]],
			['.', ['extended' => true]],
			['..', ['extended' => true]],
			['._', ['extended' => true]],
			['_.', ['extended' => true]],
			['_.1', ['extended' => true]],
			['1._', ['extended' => true]],
			['_._.', ['extended' => true]],
			['foo bar', ['extended' => true]],
			['foo..bar', ['extended' => true]],
			['foo. .bar', ['extended' => true]],
			['.foo.BAR.', ['extended' => true]],
			['foo.123bar', ['extended' => true]],
			['foo:bar123', ['extended' => true]],
			['123.__.foobar', ['extended' => true]],
			['123__.__.foobar', ['extended' => true]],
			['F00_123.Bar567.__4__', ['case' => ETextCase::LOWER, 'extended' => true]],
			['F00_123.Bar567.__4__', ['case' => ETextCase::UPPER, 'extended' => true]]
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
			$d[0] = new IdentifierTest_Class($d[0]);
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
		$this->assertInstanceOf(Text::class, Component::build(Prototype::class)->getExplanation());
	}
}



/** Test case dummy class. */
class IdentifierTest_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
