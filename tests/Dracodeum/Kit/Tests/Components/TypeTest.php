<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Components\Type\Exceptions;
use Dracodeum\Kit\Prototypes\Type\Interfaces\{
	Textifier as ITextifier,
	InformationProducer as IInformationProducer
};
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Dracodeum\Kit\Options\Text as TextOptions;
use stdClass;

/** @see \Dracodeum\Kit\Components\Type */
class TypeTest extends TestCase
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
		$component1 = Component::build(TypeTest_Prototype1::class);
		$component2 = Component::build(TypeTest_Prototype2::class);
		
		//null
		$value1 = $value2 = null;
		$error1 = $component1->process($value1);
		$error2 = $component2->process($value2);
		$this->assertNull($value1);
		$this->assertNull($value2);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertInstanceOf(Error::class, $error2);
		$this->assertTrue($error1->hasText());
		$this->assertTrue($error2->hasText());
		
		//value1 (error 1)
		$value1 = $v1 = 'foo';
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertNotSame((string)$error1->getText(), TypeTest_Prototype1::ERROR_STRING);
		
		//value1 (error 2)
		$value1 = $v1 = '50';
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertNotSame((string)$error1->getText(), TypeTest_Prototype1::ERROR_STRING);
		
		//value1 (error 3)
		$value1 = $v1 = 120.5;
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertSame((string)$error1->getText(), TypeTest_Prototype1::ERROR_STRING);
		
		//value1 (success 1)
		$value1 = 75.5;
		$this->assertNull($component1->process($value1));
		$this->assertSame(75, $value1);
		
		//value1 (success 2)
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$value1 = '50';
				$this->assertNull($component1->process($value1, $context));
				$this->assertSame(50, $value1);
			}
		}
		
		//value2 (error 1)
		$value2 = $v2 = 'foo';
		$error2 = $component2->process($value2);
		$this->assertSame($v2, $value2);
		$this->assertInstanceOf(Error::class, $error2);
		$this->assertTrue($error2->hasText());
		
		//value2 (error 2)
		foreach (EContext::getValues() as $context) {
			if ($context !== EContext::INTERNAL) {
				$value2 = $v2 = stdClass::class;
				$error2 = $component2->process($value2, $context);
				$this->assertSame($v2, $value2);
				$this->assertInstanceOf(Error::class, $error2);
				$this->assertTrue($error2->hasText());
			}
		}
		
		//value2 (success 1)
		$value2 = $v2 = new stdClass();
		$this->assertNull($component2->process($value2));
		$this->assertSame($v2, $value2);
		
		//value2 (success 2)
		$value2 = stdClass::class;
		$this->assertNull($component2->process($value2));
		$this->assertInstanceOf(stdClass::class, $value2);
	}
	
	/**
	 * Test process (nullable).
	 * 
	 * @testdox Process (nullable)
	 * 
	 * @return void
	 */
	public function testProcess_Nullable(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class, ['nullable' => true]);
		$component2 = Component::build('?' . TypeTest_Prototype2::class);
		
		//process
		$value1 = $value2 = null;
		$error1 = $component1->process($value1);
		$error2 = $component2->process($value2);
		
		//assert
		$this->assertNull($value1);
		$this->assertNull($value2);
		$this->assertNull($error1);
		$this->assertNull($error2);
	}
	
	/**
	 * Test textify.
	 * 
	 * @testdox Textify
	 * 
	 * @return void
	 */
	public function testTextify(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class, ['nullable' => true]);
		$component2 = Component::build(TypeTest_Prototype2::class);
		$component3 = Component::build(TypeTest_Prototype3::class);
		
		//assert
		foreach ([false, true] as $no_throw) {
			//text1 (1)
			$text1 = $component1->textify(108.5, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('1 0 8', (string)$text1);
			
			//text1 (2)
			$text1 = $component1->textify('-79102.75', EContext::INTERFACE, $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('- 7 9 1 0 2', (string)$text1);
			
			//text1 (3)
			$text1 = $component1->textify(null, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('null', (string)$text1);
			
			//text2 (1)
			$text2 = $component2->textify(new TypeTest_Class1(), no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text2);
			$this->assertSame('Class1', (string)$text2);
			
			//text2 (2)
			$text2 = $component2->textify(new TypeTest_Class2(), no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text2);
			$this->assertSame('__Class2', (string)$text2);
			
			//text2 (3)
			$text2 = $component2->textify(TypeTest_Class2::class, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text2);
			$this->assertSame('__Class2', (string)$text2);
			
			//text3 (1)
			$text3 = $component3->textify(1.75, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text3);
			$this->assertSame('1.75', (string)$text3);
			
			//text3 (2)
			$text3 = $component3->textify(null, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text3);
			$this->assertSame('null', (string)$text3);
		}
	}
	
	/**
	 * Test textify expecting a <code>TextificationFailed</code> exception to be thrown.
	 * 
	 * @dataProvider provideTextifyData_Exception_TextificationFailed
	 * @testdox Textify TextificationFailed exception
	 * 
	 * @param string $prototype
	 * <p>The prototype parameter to test with.</p>
	 * @param mixed $value
	 * <p>The value parameter to test with.</p>
	 * @param enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context
	 * <p>The context parameter to test with.</p>
	 * @param bool $null_error
	 * <p>Expect the exception error property to be <code>null</code>.</p>
	 * @return void
	 */
	public function testTextify_Exception_TextificationFailed(
		string $prototype, mixed $value, $context, bool $null_error
	): void
	{
		$component = null;
		$this->expectException(Exceptions\TextificationFailed::class);
		try {
			$component = Component::build($prototype);
			$component->textify($value, $context);
		} catch (Exceptions\TextificationFailed $exception) {
			$this->assertSame($component, $exception->component);
			$this->assertInstanceOf($prototype, $exception->prototype);
			$this->assertSame($value, $exception->value);
			$this->assertSame($context, $exception->context);
			if ($null_error) {
				$this->assertNull($exception->error);
			} else {
				$this->assertNotNull($exception->error);
			}
			throw $exception;
		}
	}
	
	/**
	 * Test textify with <var>$no_throw</var> set to boolean <code>true</code>, 
	 * expecting <code>null</code> to be returned.
	 * 
	 * @dataProvider provideTextifyData_Exception_TextificationFailed
	 * @testdox Textify (no throw ==> null)
	 * 
	 * @param string $prototype
	 * <p>The prototype parameter to test with.</p>
	 * @param mixed $value
	 * <p>The value parameter to test with.</p>
	 * @param enum:value(Dracodeum\Kit\Components\Type\Enumerations\Context) $context
	 * <p>The context parameter to test with.</p>
	 * @return void
	 */
	public function testTextify_NoThrow_Null(string $prototype, mixed $value, $context): void
	{
		$this->assertNull(Component::build($prototype)->textify($value, $context, true));
	}
	
	/**
	 * Provide textify data for a <code>TextificationFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The provided textify data for a <code>TextificationFailed</code> exception to be thrown.</p>
	 */
	public function provideTextifyData_Exception_TextificationFailed(): array
	{
		return [
			[TypeTest_Prototype1::class, '-79102.75', EContext::INTERNAL, false],
			[TypeTest_Prototype1::class, null, EContext::INTERNAL, false],
			[TypeTest_Prototype2::class, stdClass::class, EContext::INTERFACE, false],
			[TypeTest_Prototype2::class, stdClass::class, EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, [], EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, new stdClass(), EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, fopen(__FILE__, 'r'), EContext::INTERNAL, true]
		];
	}
	
	/**
	 * Test label.
	 * 
	 * @testdox Label
	 * 
	 * @return void
	 */
	public function testLabel(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class);
		$component2 = Component::build(TypeTest_Prototype2::class);
		
		//labels
		$label1 = $component1->getLabel();
		$label1_interface = $component1->getLabel(EContext::INTERFACE);
		$label2 = $component2->getLabel();
		
		//assert
		$this->assertInstanceOf(Text::class, $label1);
		$this->assertInstanceOf(Text::class, $label1_interface);
		$this->assertSame(TypeTest_Prototype1::LABEL_STRING_INTERNAL, (string)$label1);
		$this->assertSame(TypeTest_Prototype1::LABEL_STRING, (string)$label1_interface);
		$this->assertNull($label2);
	}
	
	/**
	 * Test description.
	 * 
	 * @testdox Description
	 * 
	 * @return void
	 */
	public function testDescription(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class);
		$component2 = Component::build(TypeTest_Prototype2::class);
		
		//descriptions
		$description1 = $component1->getDescription();
		$description1_interface = $component1->getDescription(EContext::INTERFACE);
		$description2 = $component2->getDescription();
		
		//assert
		$this->assertInstanceOf(Text::class, $description1);
		$this->assertInstanceOf(Text::class, $description1_interface);
		$this->assertSame(TypeTest_Prototype1::DESCRIPTION_STRING_INTERNAL, (string)$description1);
		$this->assertSame(TypeTest_Prototype1::DESCRIPTION_STRING, (string)$description1_interface);
		$this->assertNull($description2);
	}
}



/** Test case dummy prototype class 1. */
class TypeTest_Prototype1 extends Prototype implements ITextifier, IInformationProducer
{
	public const ERROR_STRING = "Cannot be greater than 100.";
	public const LABEL_STRING = "Test 1";
	public const LABEL_STRING_INTERNAL = "test1";
	public const DESCRIPTION_STRING = "This is a testing type.";
	public const DESCRIPTION_STRING_INTERNAL = "Testing type.";
	
	
	
	public function process(mixed &$value, $context): ?Error
	{
		//context
		if ($context !== EContext::INTERNAL && is_string($value) && is_numeric($value)) {
			$value = (float)$value;
		}
		
		//process
		if (!is_int($value) && !is_float($value)) {
			return Error::build();
		} else {
			$value = (int)$value;
			if ($value > 110) {
				return Error::build(text: self::ERROR_STRING);
			}
		}
		return null;
	}
	
	public function textify(mixed $value)
	{
		return implode(' ', str_split($value));
	}
	
	public function produceLabel($context)
	{
		return $context === EContext::INTERNAL ? self::LABEL_STRING_INTERNAL : self::LABEL_STRING;
	}
	
	public function produceDescription($context)
	{
		return $context === EContext::INTERNAL ? self::DESCRIPTION_STRING_INTERNAL : self::DESCRIPTION_STRING;
	}
}



/** Test case dummy prototype class 2. */
class TypeTest_Prototype2 extends Prototype
{
	public function process(mixed &$value, $context): ?Error
	{
		if ($context === EContext::INTERNAL && is_string($value) && class_exists($value)) {
			$value = new $value();
		}
		return is_object($value) ? null : Error::build();
	}
}



/** Test case dummy prototype class 3. */
class TypeTest_Prototype3 extends Prototype
{
	public function process(mixed &$value, $context): ?Error
	{
		return null;
	}
}



/** Test case dummy class 1. */
class TypeTest_Class1 implements IStringable
{
	public function toString(?TextOptions $text_options = null): string
	{
		return "Class1";
	}
	
	public function __toString(): string
	{
		return "__Class1";
	}
}



/** Test case dummy class 2. */
class TypeTest_Class2
{
	public function __toString(): string
	{
		return "__Class2";
	}
}
