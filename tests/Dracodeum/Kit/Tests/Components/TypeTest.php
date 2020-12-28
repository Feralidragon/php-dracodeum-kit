<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\Type as Prototype;
use Dracodeum\Kit\Prototypes\Type\Interfaces\InformationProducer as IInformationProducer;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Components\Type */
class TypeTest extends TestCase
{
	//Public methods
	/**
	 * Test process.
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
	 * Test process nullable.
	 * 
	 * @return void
	 */
	public function testProcessNullable(): void
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
	 * Test label.
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



/** Test case dummy prototype 1 class. */
class TypeTest_Prototype1 extends Prototype implements IInformationProducer
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
			if ($value > 100) {
				return Error::build(text: self::ERROR_STRING);
			}
		}
		return null;
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



/** Test case dummy prototype 2 class. */
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
