<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Stringables;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Base64 as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Stringables\Base64 */
class Base64Test extends TestCase
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
	public function testProcess(mixed $value, array $properties = []): void
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
	public function testProcess_Error(mixed $value, array $properties = []): void
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
			['aBCd'],
			['aB/C'],
			['aB/Cd3'],
			['aB/Cd3=='],
			['aB/Cd3+'],
			['aB/Cd3+='],
			['aB_Cd3-'],
			['aBCd', ['url_safe' => false]],
			['aB/Cd3+', ['url_safe' => false]],
			['aB/Cd3+=', ['url_safe' => false]],
			['aBCd', ['url_safe' => true]],
			['aB_Cd3-', ['url_safe' => true]]
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
			$d[0] = new Base64Test_Class($d[0]);
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
			[''],
			[' '],
			['='],
			['a'],
			['$a#b'],
			['aB/C='],
			['aB/C=='],
			['aB/Cd'],
			['aB/Cd='],
			['aB/Cd=='],
			['aB/Cd3='],
			['aB/Cd3+=='],
			['aB_Cd3-='],
			['aB_Cd3-=='],
			['aB/Cd3-'],
			['aB_Cd3+='],
			['', ['url_safe' => false]],
			[' ', ['url_safe' => false]],
			['=', ['url_safe' => false]],
			['a', ['url_safe' => false]],
			['$a#b', ['url_safe' => false]],
			['aB/Cd3+==', ['url_safe' => false]],
			['aB_Cd3-', ['url_safe' => false]],
			['aB_Cd3-=', ['url_safe' => false]],
			['aB_Cd3-==', ['url_safe' => false]],
			['aB/Cd3-', ['url_safe' => false]],
			['aB_Cd3+=', ['url_safe' => false]],
			['', ['url_safe' => true]],
			[' ', ['url_safe' => true]],
			['=', ['url_safe' => true]],
			['a', ['url_safe' => true]],
			['$a#b', ['url_safe' => true]],
			['aB/Cd3+', ['url_safe' => true]],
			['aB/Cd3+=', ['url_safe' => true]],
			['aB/Cd3+==', ['url_safe' => true]],
			['aB_Cd3-=', ['url_safe' => true]],
			['aB_Cd3-==', ['url_safe' => true]],
			['aB/Cd3-', ['url_safe' => true]],
			['aB_Cd3+=', ['url_safe' => true]]
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
			$d[0] = new Base64Test_Class($d[0]);
		}
		unset($d);
		return $data;
	}
}



/** Test case dummy class. */
class Base64Test_Class
{
	public function __construct(private string $string) {}
	
	public function __toString(): string
	{
		return $this->string;
	}
}
