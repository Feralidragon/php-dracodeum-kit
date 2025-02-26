<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutators\Numericals;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Even as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @covers \Dracodeum\Kit\Components\Type\Prototypes\Mutators\Numericals\Even */
class EvenTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * @dataProvider provideProcessData
	 * 
	 * @param mixed $value
	 * The value to test with.
	 */
	public function testProcess(mixed $value): void
	{
		$v = $value;
		$this->assertNull(Component::build(Prototype::class)->process($value));
		$this->assertSame($v, $value);
	}
	
	/**
	 * Test process (error).
	 * 
	 * @testdox Process (error)
	 * @dataProvider provideProcessData_Error
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
			[0],
			[2],
			[4],
			[6],
			[8],
			[10],
			[124],
			[7530],
			[-2],
			[-4],
			[-6],
			[-8],
			[-10],
			[-124],
			[-7530],
			[2.0],
			[4.0],
			[6.0],
			[8.0],
			[10.0],
			[124.0],
			[7530.0],
			[-2.0],
			[-4.0],
			[-6.0],
			[-8.0],
			[-10.0],
			[-124.0],
			[-7530.0]
		];
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
			[1],
			[3],
			[5],
			[7],
			[9],
			[11],
			[123],
			[8641],
			[-1],
			[-3],
			[-5],
			[-7],
			[-9],
			[-11],
			[-123],
			[-8641],
			[1.0],
			[3.0],
			[5.0],
			[7.0],
			[9.0],
			[11.0],
			[123.0],
			[8641.0],
			[-1.0],
			[-3.0],
			[-5.0],
			[-7.0],
			[-9.0],
			[-11.0],
			[-123.0],
			[-8641.0],
			[0.1],
			[0.2],
			[1.1],
			[1.2],
			[2.1],
			[2.2],
			[123.8],
			[123.9],
			[124.8],
			[124.9],
			[7530.4],
			[8641.5],
			[-0.1],
			[-0.2],
			[-1.1],
			[-1.2],
			[-2.1],
			[-2.2],
			[-123.8],
			[-123.9],
			[-124.8],
			[-124.9],
			[-7530.4],
			[-8641.5]
		];
	}
}
