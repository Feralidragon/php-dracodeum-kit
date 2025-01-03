<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type as Component;
use Dracodeum\Kit\Prototypes\{
	Type as Prototype,
	Types as Prototypes
};
use Dracodeum\Kit\Components\Type\Exceptions;
use Dracodeum\Kit\Components\Type\Components\Mutator as MutatorComponent;
use Dracodeum\Kit\Components\Type\Prototypes\Mutator as MutatorPrototype;
use Dracodeum\Kit\Components\Type\Enumerations\Context as EContext;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\{
	Component as KComponent,
	Structure as KStructure,
	Enumeration
};
use ReflectionProperty;
use stdClass;

/** @covers \Dracodeum\Kit\Components\Type */
class TypeTest extends TestCase
{
	//Public methods
	/**
	 * Test build.
	 * 
	 * @testdox Build
	 */
	public function testBuild(): void
	{
		//build
		$component = Component::build(TypeTest_Prototype1::class);
		
		//assert
		$this->assertInstanceOf(Component::class, $component);
		$this->assertFalse($component->hasMutators());
	}
	
	/**
	 * Test prototype name.
	 * 
	 * @testdox Prototype name ("$name")
	 * @dataProvider providePrototypeNameData
	 * 
	 * @param string $name
	 * The name to test with.
	 * 
	 * @param string $expected
	 * The expected prototype class.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testPrototypeName(string $name, string $expected, array $properties = []): void
	{
		//build
		$component = Component::build($name, $properties);
		
		//reflection
		$reflection = new ReflectionProperty(KComponent::class, 'prototype');
		$reflection->setAccessible(true);
		
		//assert
		$this->assertInstanceOf(Component::class, $component);
		$this->assertInstanceOf($expected, $reflection->getValue($component));
	}
	
	/**
	 * Test process.
	 * 
	 * @testdox Process
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
		$this->assertNotSame('', (string)$error1->getText());
		$this->assertNotSame(TypeTest_Prototype1::ERROR_STRING, (string)$error1->getText());
		
		//value1 (error 2)
		$value1 = $v1 = '50';
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertNotSame('', (string)$error1->getText());
		$this->assertNotSame(TypeTest_Prototype1::ERROR_STRING, (string)$error1->getText());
		
		//value1 (error 3)
		$value1 = $v1 = 120.5;
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertSame(TypeTest_Prototype1::ERROR_STRING, (string)$error1->getText());
		
		//value1 (error 4)
		$value1 = $v1 = new stdClass;
		$error1 = $component1->process($value1);
		$this->assertSame($v1, $value1);
		$this->assertInstanceOf(Error::class, $error1);
		$this->assertTrue($error1->hasText());
		$this->assertNotSame('', $error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER->value]));
		$this->assertNotSame(
			TypeTest_Prototype1::ERROR_STRING,
			$error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER->value])
		);
		$this->assertNotSame(
			TypeTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::ENDUSER->value])
		);
		$this->assertSame(
			TypeTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::TECHNICAL->value])
		);
		$this->assertSame(
			TypeTest_Prototype1::ERROR_STRING_TECHNICAL,
			$error1->getText()->toString(['info_level' => EInfoLevel::INTERNAL->value])
		);
		
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
		$this->assertNotSame('', (string)$error2->getText());
		
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
		$value2 = $v2 = new stdClass;
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
	 * @dataProvider provideProcessData_Nullable
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param array $properties
	 * The properties to test with.
	 */
	public function testProcess_Nullable(string $prototype, array $properties = []): void
	{
		$value = null;
		$error = Component::build($prototype, $properties)->process($value);
		$this->assertNull($error);
		$this->assertNull($value);
	}
	
	/**
	 * Test process (strict).
	 * 
	 * @testdox Process (strict)
	 */
	public function testProcess_Strict(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class, ['strict' => true]);
		$component2 = Component::build(TypeTest_Prototype2::class, ['strict' => true]);
		
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
		
		//value1 (errors)
		foreach (['foo', '50', 120.5, 75.5, 75.0] as $v1) {
			$value1 = $v1;
			$error1 = $component1->process($value1);
			$this->assertSame($v1, $value1);
			$this->assertInstanceOf(Error::class, $error1);
			$this->assertTrue($error1->hasText());
			$this->assertSame(TypeTest_Prototype1::ERROR_STRING_STRICT, (string)$error1->getText());
		}
		
		//value1 (success)
		$value1 = $v1 = 75;
		$this->assertNull($component1->process($value1));
		$this->assertSame($v1, $value1);
		
		//value2 (errors)
		foreach (['foo', stdClass::class] as $v2) {
			$value2 = $v2;
			$error2 = $component2->process($value2);
			$this->assertSame($v2, $value2);
			$this->assertInstanceOf(Error::class, $error2);
			$this->assertTrue($error2->hasText());
			$this->assertNotSame('', (string)$error2->getText());
		}
		
		//value2 (success)
		$value2 = $v2 = new stdClass;
		$this->assertNull($component2->process($value2));
		$this->assertSame($v2, $value2);
	}
	
	/**
	 * Test process cast.
	 * 
	 * @testdox ProcessCast
	 */
	public function testProcessCast(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class);
		$component2 = Component::build(TypeTest_Prototype2::class);
		
		//assert
		foreach ([false, true] as $no_throw) {
			//value (1)
			$this->assertSame(75, $component1->processCast(75.5, no_throw: $no_throw));
			
			//value (2)
			foreach (EContext::getValues() as $context) {
				if ($context !== EContext::INTERNAL) {
					$this->assertSame(50, $component1->processCast('50', $context, $no_throw));
				}
			}
			
			//value (3)
			$value = new stdClass;
			$this->assertSame($value, $component2->processCast($value, no_throw: $no_throw));
			
			//value (4)
			$this->assertInstanceOf(stdClass::class, $component2->processCast(stdClass::class, no_throw: $no_throw));
		}
	}
	
	/**
	 * Test process cast expecting a `CastFailed` exception to be thrown.
	 * 
	 * @testdox ProcessCast CastFailed exception
	 * @dataProvider provideProcessCastData_Exception_CastFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 * 
	 * @param string|null $expected_error_string
	 * The expected error string.
	 * 
	 * @param coercible<\Dracodeum\Kit\Options\Text> $error_text_options
	 * The error text options to test with.
	 * 
	 * @param bool $error_string_not_same
	 * Expect the error string to not be the same as the expected error string.
	 */
	public function testProcessCast_Exception_CastFailed(
		string $prototype, mixed $value, $context = EContext::INTERNAL, ?string $expected_error_string = null,
		$error_text_options = null, bool $error_string_not_same = false
	): void
	{
		$component = Component::build($prototype);
		$this->expectException(Exceptions\CastFailed::class);
		try {
			$component->processCast($value, $context);
		} catch (Exceptions\CastFailed $exception) {
			$this->assertSame($component, $exception->component);
			$this->assertSame($value, $exception->value);
			$this->assertSame($context, $exception->context);
			$this->assertNotNull($exception->error);
			$this->assertNotSame('', (string)$exception->error->getText());
			if ($expected_error_string !== null) {
				$error_string = $exception->error->getText()->toString($error_text_options);
				if ($error_string_not_same) {
					$this->assertNotSame($expected_error_string, $error_string);
				} else {
					$this->assertSame($expected_error_string, $error_string);
				}
			}
			throw $exception;
		}
	}
	
	/**
	 * Test process cast with `$no_throw` set to boolean `true`, expecting `null` to be returned.
	 * 
	 * @testdox ProcessCast (no throw ==> null)
	 * @dataProvider provideProcessCastData_Exception_CastFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 */
	public function testProcessCast_NoThrow_Null(string $prototype, mixed $value, $context = EContext::INTERNAL): void
	{
		$this->assertNull(Component::build($prototype)->processCast($value, $context, true));
	}
	
	/**
	 * Test process coercion.
	 * 
	 * @testdox ProcessCoercion
	 */
	public function testProcessCoercion(): void
	{
		//build
		$component1 = Component::build(TypeTest_Prototype1::class);
		$component2 = Component::build(TypeTest_Prototype2::class);
		
		//assert
		foreach ([false, true] as $no_throw) {
			//value (1)
			$value = 75.5;
			$this->assertTrue($component1->processCoercion2($value, no_throw: $no_throw));
			$this->assertSame(75, $value);
			
			//value (2)
			foreach (EContext::getValues() as $context) {
				if ($context !== EContext::INTERNAL) {
					$value = '50';
					$this->assertTrue($component1->processCoercion2($value, $context, $no_throw));
					$this->assertSame(50, $value);
				}
			}
			
			//value (3)
			$value = $v = new stdClass;
			$this->assertTrue($component2->processCoercion2($value, no_throw: $no_throw));
			$this->assertSame($v, $value);
			
			//value (4)
			$value = stdClass::class;
			$this->assertTrue($component2->processCoercion2($value, no_throw: $no_throw));
			$this->assertInstanceOf(stdClass::class, $value);
		}
	}
	
	/**
	 * Test process coercion expecting a `CoercionFailed` exception to be thrown.
	 * 
	 * @testdox ProcessCoercion CoercionFailed exception
	 * @dataProvider provideProcessCastData_Exception_CastFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 * 
	 * @param string|null $expected_error_string
	 * The expected error string.
	 * 
	 * @param coercible<\Dracodeum\Kit\Options\Text> $error_text_options
	 * The error text options to test with.
	 * 
	 * @param bool $error_string_not_same
	 * Expect the error string to not be the same as the expected error string.
	 */
	public function testProcessCoercion_Exception_CoercionFailed(
		string $prototype, mixed $value, $context = EContext::INTERNAL, ?string $expected_error_string = null,
		$error_text_options = null, bool $error_string_not_same = false
	): void
	{
		$v = $value;
		$component = Component::build($prototype);
		$this->expectException(Exceptions\CoercionFailed::class);
		try {
			$component->processCoercion2($v, $context);
		} catch (Exceptions\CoercionFailed $exception) {
			$this->assertSame($value, $v);
			$this->assertSame($component, $exception->component);
			$this->assertSame($value, $exception->value);
			$this->assertSame($context, $exception->context);
			$this->assertNotNull($exception->error);
			$this->assertNotSame('', (string)$exception->error->getText());
			if ($expected_error_string !== null) {
				$error_string = $exception->error->getText()->toString($error_text_options);
				if ($error_string_not_same) {
					$this->assertNotSame($expected_error_string, $error_string);
				} else {
					$this->assertSame($expected_error_string, $error_string);
				}
			}
			throw $exception;
		}
	}
	
	/**
	 * Test process coercion with `$no_throw` set to boolean `true`, expecting boolean `false` to be returned.
	 * 
	 * @testdox ProcessCoercion (no throw ==> false)
	 * @dataProvider provideProcessCastData_Exception_CastFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 */
	public function testProcessCoercion_NoThrow_False(
		string $prototype, mixed $value, $context = EContext::INTERNAL
	): void
	{
		$v = $value;
		$this->assertFalse(Component::build($prototype)->processCoercion2($v, $context, true));
		$this->assertSame($value, $v);
	}
	
	/**
	 * Test textify.
	 * 
	 * @testdox Textify
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
			$text1 = $component1->textify(105, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('105', (string)$text1);
			
			//text1 (2)
			$text1 = $component1->textify(108.5, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('108', (string)$text1);
			
			//text1 (3)
			$text1 = $component1->textify(null, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text1);
			$this->assertSame('null', (string)$text1);
			
			//text2 (1)
			$text2 = $component2->textify(new TypeTest_Class1, no_throw: $no_throw);
			$this->assertInstanceOf(Text::class, $text2);
			$this->assertSame('Class1', (string)$text2);
			
			//text2 (2)
			$text2 = $component2->textify(new TypeTest_Class2, no_throw: $no_throw);
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
	 * Test textify expecting a `TextificationFailed` exception to be thrown.
	 * 
	 * @testdox Textify TextificationFailed exception
	 * @dataProvider provideTextifyData_Exception_TextificationFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 * 
	 * @param bool $null_error
	 * Expect the exception error property to be `null`.
	 */
	public function testTextify_Exception_TextificationFailed(
		string $prototype, mixed $value, $context, bool $null_error
	): void
	{
		$component = Component::build($prototype);
		$this->expectException(Exceptions\TextificationFailed::class);
		try {
			$component->textify($value, $context);
		} catch (Exceptions\TextificationFailed $exception) {
			$this->assertSame($component, $exception->component);
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
	 * Test textify with `$no_throw` set to boolean `true`, expecting `null` to be returned.
	 * 
	 * @testdox Textify (no throw ==> null)
	 * @dataProvider provideTextifyData_Exception_TextificationFailed
	 * 
	 * @param string $prototype
	 * The prototype to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to test with.
	 */
	public function testTextify_NoThrow_Null(string $prototype, mixed $value, $context): void
	{
		$this->assertNull(Component::build($prototype)->textify($value, $context, true));
	}
	
	/**
	 * Test mutators.
	 * 
	 * @testdox Mutators
	 * @dataProvider provideMutatorsData
	 * 
	 * @param \Dracodeum\Kit\Components\Type $component
	 * The component instance to test with.
	 * 
	 * @param mixed $value
	 * The value to test with.
	 * 
	 * @param mixed $expected
	 * The expected processed value.
	 */
	public function testMutators(Component $component, mixed $value, mixed $expected): void
	{
		$this->assertTrue($component->hasMutators());
		$this->assertNull($component->process($value));
		$this->assertSame($expected, $value);
	}
	
	/**
	 * Test mutators (error).
	 * 
	 * @testdox Mutators (error)
	 */
	public function testMutators_Error(): void
	{
		//build
		$component = Component::build(TypeTest_Prototype3::class)
			->addMutator(TypeTest_MutatorPrototype1::class)
			->addMutator(TypeTest_MutatorPrototype2::class, [1000])
		;
		
		//check
		$this->assertTrue($component->hasMutators());
		
		//error 1
		$value = $v = '65';
		$error = $component->process($value);
		$this->assertSame($v, $value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertSame(TypeTest_MutatorPrototype1::ERROR_STRING, (string)$error->getText());
		
		//error 2
		$value = $v = '15';
		$error = $component->process($value);
		$this->assertSame($v, $value);
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasText());
		$this->assertNotSame('', (string)$error->getText());
		$this->assertNotSame(TypeTest_MutatorPrototype1::ERROR_STRING, (string)$error->getText());
	}
	
	
	
	//Public static methods
	/**
	 * Provide prototype name data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function providePrototypeNameData(): array
	{
		return [
			['any', Prototypes\Any::class],
			['mixed', Prototypes\Any::class],
			['boolean', Prototypes\Boolean::class],
			['bool', Prototypes\Boolean::class],
			['number', Prototypes\Number::class],
			['integer', Prototypes\Number::class],
			['int', Prototypes\Number::class],
			['float', Prototypes\Number::class],
			['double', Prototypes\Number::class],
			['string', Prototypes\TString::class],
			['ustring', Prototypes\TString::class],
			['enumeration', Prototypes\Enumeration::class, [TypeTest_Enum::class]],
			['enum', Prototypes\Enumeration::class, [TypeTest_Enum::class]],
			['class', Prototypes\TClass::class],
			['class<stdClass>', Prototypes\TClass::class],
			['class< stdClass >', Prototypes\TClass::class],
			['interface', Prototypes\TInterface::class],
			['object', Prototypes\TObject::class],
			['object<stdClass>', Prototypes\TObject::class],
			['object< stdClass >', Prototypes\TObject::class],
			['resource', Prototypes\TResource::class],
			['resource<stream>', Prototypes\TResource::class],
			['resource< stream >', Prototypes\TResource::class],
			['callable', Prototypes\TCallable::class],
			['closure', Prototypes\TCallable::class],
			['array', Prototypes\TArray::class],
			['array<string>', Prototypes\TArray::class],
			['array< string >', Prototypes\TArray::class],
			['array<string,int>', Prototypes\TArray::class],
			['array< string , int >', Prototypes\TArray::class],
			['list', Prototypes\TArray::class],
			['component', Prototypes\Component::class, [Component::class]],
			['structure', Prototypes\Structure::class, [TypeTest_Struct::class]],
			['struct', Prototypes\Structure::class, [TypeTest_Struct::class]],
			['text', Prototypes\Text::class],
			['stdClass', Prototypes\TObject::class],
			['boolean|integer', Prototypes\Any::class],
			['boolean | integer', Prototypes\Any::class],
			['boolean|integer|ustring', Prototypes\Any::class],
			['boolean | integer | ustring', Prototypes\Any::class],
			['boolean|integer[]', Prototypes\Any::class],
			['boolean | integer[]', Prototypes\Any::class],
			['boolean[]|integer[]|ustring[]', Prototypes\Any::class],
			['boolean[] | integer[] | ustring[]', Prototypes\Any::class],
			['boolean[]', Prototypes\TArray::class],
			['integer[]', Prototypes\TArray::class],
			['ustring[]', Prototypes\TArray::class],
			['(boolean)[]', Prototypes\TArray::class],
			['(integer)[]', Prototypes\TArray::class],
			['(ustring)[]', Prototypes\TArray::class],
			['(boolean|integer)[]', Prototypes\TArray::class],
			['(boolean | integer)[]', Prototypes\TArray::class],
			['(boolean|integer|ustring)[]', Prototypes\TArray::class],
			['(boolean | integer | ustring)[]', Prototypes\TArray::class],
			['(boolean|integer[])[]', Prototypes\TArray::class],
			['(boolean | integer[])[]', Prototypes\TArray::class],
			['(boolean[]|integer[]|ustring[])[]', Prototypes\TArray::class],
			['(boolean[] | integer[] | ustring[])[]', Prototypes\TArray::class],
			['array<string,(boolean[] | integer[] | ustring[] | class< stdClass >[])[]>', Prototypes\TArray::class]
		];
	}
	
	/**
	 * Provide process data (nullable).
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessData_Nullable(): array
	{
		//initialize
		$prototype1_class = TypeTest_Prototype1::class;
		$prototype2_class = TypeTest_Prototype2::class;
		
		//return
		return [
			[$prototype1_class, ['nullable' => true]],
			["?{$prototype2_class}"],
			["{$prototype2_class}|null"],
			["{$prototype2_class} | null"],
			["null|{$prototype2_class}"],
			["null | {$prototype2_class}"],
			["{$prototype1_class}|null|{$prototype2_class}"],
			["{$prototype1_class} | null | {$prototype2_class}"]
		];
	}
	
	/**
	 * Provide process cast data for a `CastFailed` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideProcessCastData_Exception_CastFailed(): array
	{
		//initialize
		$prototype1 = TypeTest_Prototype1::class;
		$prototype2 = TypeTest_Prototype2::class;
		
		//return
		return [
			[$prototype1, null],
			[$prototype2, null],
			[$prototype1, '-79102.75'],
			[$prototype2, 'foo'],
			[$prototype2, stdClass::class, EContext::CONFIGURATION],
			[$prototype2, stdClass::class, EContext::INTERFACE],
			[$prototype1, 120.5, EContext::INTERNAL, $prototype1::ERROR_STRING],
			[$prototype1, '50', EContext::INTERNAL, $prototype1::ERROR_STRING, null, true],
			[$prototype1, 'foo', EContext::INTERNAL, $prototype1::ERROR_STRING, null, true],
			[$prototype1, new stdClass, EContext::INTERNAL, $prototype1::ERROR_STRING,
				['info_level' => EInfoLevel::ENDUSER->value], true],
			[$prototype1, new stdClass, EContext::INTERNAL, $prototype1::ERROR_STRING_TECHNICAL,
				['info_level' => EInfoLevel::ENDUSER->value], true],
			[$prototype1, new stdClass, EContext::INTERNAL, $prototype1::ERROR_STRING_TECHNICAL,
				['info_level' => EInfoLevel::TECHNICAL->value]],
			[$prototype1, new stdClass, EContext::INTERNAL, $prototype1::ERROR_STRING_TECHNICAL,
				['info_level' => EInfoLevel::INTERNAL->value]]
		];
	}
	
	/**
	 * Provide textify data for a `TextificationFailed` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideTextifyData_Exception_TextificationFailed(): array
	{
		return [
			[TypeTest_Prototype1::class, '-79102.75', EContext::INTERNAL, false],
			[TypeTest_Prototype1::class, null, EContext::INTERNAL, false],
			[TypeTest_Prototype2::class, stdClass::class, EContext::INTERFACE, false],
			[TypeTest_Prototype2::class, stdClass::class, EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, [], EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, new stdClass, EContext::INTERNAL, true],
			[TypeTest_Prototype3::class, fopen(__FILE__, 'r'), EContext::INTERNAL, true]
		];
	}
	
	/**
	 * Provide mutators data.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideMutatorsData(): array
	{
		return [[
			Component::build(TypeTest_Prototype3::class)
				->addMutator(TypeTest_MutatorPrototype1::class)
				->addMutator(TypeTest_MutatorPrototype2::class, [1000])
			, '35', 1735.0
		], [
			Component::build(TypeTest_Prototype3::class)
				->addMutator(TypeTest_MutatorPrototype1::class, ['amount' => 850.5])
				->addMutator(TypeTest_MutatorPrototype2::class, ['amount' => 37])
			, '48', 935.5
		], [
			Component::build(TypeTest_Prototype3::class)
				->addMutator(MutatorComponent::build(TypeTest_MutatorPrototype1::class))
				->addMutator(MutatorComponent::build(TypeTest_MutatorPrototype2::class, [1000]))
			, '35', 1735.0
		], [
			Component::build(TypeTest_Prototype3::class)
				->addMutator(MutatorComponent::build(TypeTest_MutatorPrototype1::class, ['amount' => 850.5]))
				->addMutator(MutatorComponent::build(TypeTest_MutatorPrototype2::class, ['amount' => 37]))
			, '48', 935.5
		]];
	}
}



/** Test case dummy prototype class 1. */
class TypeTest_Prototype1 extends Prototype
{
	public const ERROR_STRING = "Cannot be greater than 100.";
	public const ERROR_STRING_TECHNICAL = "Cannot be an object.";
	public const ERROR_STRING_STRICT = "Must strictly be an integer.";
	
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		//strict
		if ($strict && !is_int($value)) {
			$value = -1;
			return Error::build(text: self::ERROR_STRING_STRICT);
		}
		
		//context
		if ($context !== EContext::INTERNAL && is_string($value) && is_numeric($value)) {
			$value = (float)$value;
		}
		
		//process
		if (is_object($value)) {
			$value = -1;
			return Error::build(text: Text::build(self::ERROR_STRING_TECHNICAL, EInfoLevel::TECHNICAL));
		} elseif (!is_int($value) && !is_float($value)) {
			$value = -1;
			return Error::build();
		} else {
			$value = (int)$value;
			if ($value > 110) {
				$value = -1;
				return Error::build(text: self::ERROR_STRING);
			}
		}
		return null;
	}
}



/** Test case dummy prototype class 2. */
class TypeTest_Prototype2 extends Prototype
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		if ($strict && !is_object($value)) {
			$value = -1;
			return Error::build();
		} elseif ($context === EContext::INTERNAL && is_string($value) && class_exists($value)) {
			$value = new $value;
		}
		if (is_object($value)) {
			return null;
		}
		$value = -1;
		return Error::build();
	}
}



/** Test case dummy prototype class 3. */
class TypeTest_Prototype3 extends Prototype
{
	public function process(mixed &$value, $context, bool $strict): ?Error
	{
		return null;
	}
}



/** Test case dummy class 1. */
class TypeTest_Class1 implements IStringable
{
	public function toString($text_options = null): string
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



/** Test case dummy mutator prototype class 1. */
class TypeTest_MutatorPrototype1 extends MutatorPrototype
{
	public const ERROR_STRING = "Must be less than 50.";
	
	private float $amount = 700.0;
	
	public function process(mixed &$value): ?Error
	{
		$value = (float)$value;
		if ($value < 50.0) {
			$value += $this->amount;
			return null;
		}
		$value = -1;
		return Error::build(text: self::ERROR_STRING);
	}
	
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'amount' => $this->createProperty()->setAsFloat()->bind(self::class),
			default => null
		};
	}
}



/** Test case dummy mutator prototype class 2. */
class TypeTest_MutatorPrototype2 extends MutatorPrototype
{
	private float $amount;
	
	public function process(mixed &$value): ?Error
	{
		$value = (float)$value;
		if ($value > 725.0) {
			$value += $this->amount;
			return null;
		}
		$value = -1;
		return Error::build();
	}
	
	protected function initializeProperties(): void
	{
		$this->addRequiredPropertyName('amount');
	}
	
	protected function buildProperty(string $name): ?Property
	{
		return match ($name) {
			'amount' => $this->createProperty()->setAsFloat()->bind(self::class),
			default => null
		};
	}
}



/** Test case dummy enumeration. */
class TypeTest_Enum extends Enumeration {}



/** Test case dummy structure. */
class TypeTest_Struct extends KStructure
{
	protected function loadProperties(): void {}
}
