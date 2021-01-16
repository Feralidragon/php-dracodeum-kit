<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type\Components;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Components\Mutator as Component;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as Prototype;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer as IExplanationProducer;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use stdClass;

/** @see \Dracodeum\Kit\Components\Type\Components\Mutator */
class MutatorTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
	 * 
	 * @testdox Process
	 * 
	 * @return void
	 */
	public function testProcess(): void
	{
		//build
		$component1 = Component::build(MutatorTest_Prototype1::class);
		$component2 = Component::build(MutatorTest_Prototype2::class);
		
		//value1 (error 1)
		$value1 = $v1 = 'Foo';
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertSame(MutatorTest_Prototype1::ERROR_STRING, (string)$error1->getText());
		
		//value1 (error 2)
		$value1 = $v1 = 123;
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertSame(MutatorTest_Prototype1::EXPLANATION_STRING, (string)$error1->getText());
		
		//value1 (error 3)
		$value1 = $v1 = new stdClass();
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertNotSame('', $error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertNotSame(
			MutatorTest_Prototype1::ERROR_STRING,
			$error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER])
		);
		$this->assertNotSame(
			MutatorTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER])
		);
		$this->assertSame(
			MutatorTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::TECHNICAL])
		);
		$this->assertSame(
			MutatorTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::INTERNAL])
		);
		
		//value1 (success)
		$value1 = 'foo';
		$this->assertNull($component1->process($value1));
		$this->assertSame('FOO', $value1);
		
		//value2 (error)
		$value2 = $v2 = 123;
		$error2 = $component2->process($value2);
		$this->assertSame($v2, $value2);
		$this->assertInstanceOf(Error::class, $error2);
		$this->assertTrue($error2->hasText());
		$this->assertNotSame('', (string)$error2->getText());
		
		//value2 (success)
		$value2 = 'bar';
		$this->assertNull($component2->process($value2));
		$this->assertSame('__bar__', $value2);
	}
	
	/**
	 * Test explanation.
	 * 
	 * @testdox Explanation
	 * 
	 * @return void
	 */
	public function testExplanation(): void
	{
		//build
		$component1 = Component::build(MutatorTest_Prototype1::class);
		$component2 = Component::build(MutatorTest_Prototype2::class);
		
		//explanations
		$explanation1 = $component1->getExplanation();
		$explanation2 = $component2->getExplanation();
		
		//assert
		$this->assertInstanceOf(Text::class, $explanation1);
		$this->assertSame(MutatorTest_Prototype1::EXPLANATION_STRING, (string)$explanation1);
		$this->assertNull($explanation2);
	}
}



/** Test case dummy prototype class 1. */
class MutatorTest_Prototype1 extends Prototype implements IExplanationProducer
{
	public const ERROR_STRING = "Must be equal to \"foo\".";
	public const ERROR_STRING_TECHNICAL = "Cannot be an object.";
	public const EXPLANATION_STRING = "Only a string equal to \"foo\" is allowed.";
	
	public function process(mixed &$value): ?Error
	{
		if ($value === 'foo') {
			$value = 'FOO';
			return null;
		} elseif (is_int($value)) {
			return Error::build();
		} elseif (is_object($value)) {
			return Error::build(text: Text::build(self::ERROR_STRING_TECHNICAL, EInfoLevel::TECHNICAL));
		}
		return Error::build(text: self::ERROR_STRING);
	}
	
	public function produceExplanation()
	{
		return self::EXPLANATION_STRING;
	}
}



/** Test case dummy prototype class 2. */
class MutatorTest_Prototype2 extends Prototype
{
	public function process(mixed &$value): ?Error
	{
		$value = (string)$value;
		if ($value === 'bar') {
			$value = '__bar__';
			return null;
		}
		return Error::build();
	}
}
