<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Prototypes\Mutator\Interfaces;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};

/** @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer */
class ExplanationProducerTest extends TestCase
{
	//Public methods
	/**
	 * Test.
	 * 
	 * @testdox Test
	 */
	public function test(): void
	{
		//build
		$component1 = Component::build(ExplanationProducerTest_Prototype1::class);
		$component2 = Component::build(ExplanationProducerTest_Prototype2::class);
		
		//explanations
		$explanation1 = $component1->getExplanation();
		$explanation2 = $component2->getExplanation();
		
		//assert
		$this->assertInstanceOf(Text::class, $explanation1);
		$this->assertInstanceOf(Text::class, $explanation2);
		$this->assertSame(ExplanationProducerTest_Prototype1::STRING, (string)$explanation1);
		$this->assertSame(ExplanationProducerTest_Prototype2::STRING, (string)$explanation2);
		$this->assertNotSame((string)$explanation1, (string)$explanation2);
	}
	
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 */
	public function testProcess(): void
	{
		//build
		$component1 = Component::build(ExplanationProducerTest_Prototype1::class);
		$component2 = Component::build(ExplanationProducerTest_Prototype2::class);
		
		//value (1)
		$value = 10;
		$error = $component1->process($value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertSame(ExplanationProducerTest_Prototype1::STRING, (string)$error->getText());
		
		//value (2)
		$value = 100;
		$error = $component2->process($value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertSame(ExplanationProducerTest_Prototype2::STRING, (string)$error->getText());
	}
}



/** Test case dummy prototype class 1. */
class ExplanationProducerTest_Prototype1 extends Prototype implements IExplanationProducer
{
	public const STRING = "Only a value greater than 50 is allowed.";
	
	public function process(mixed &$value)
	{
		return $value > 50;
	}
	
	public function produceExplanation()
	{
		return self::STRING;
	}
}



/** Test case dummy prototype class 2. */
class ExplanationProducerTest_Prototype2 extends Prototype implements IExplanationProducer
{
	public const STRING = "Only a value lesser than 25 is allowed.";
	
	public function process(mixed &$value)
	{
		return $value < 25;
	}
	
	public function produceExplanation()
	{
		return Text::build(self::STRING);
	}
}
