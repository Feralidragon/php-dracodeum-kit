<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Primitives;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Primitives\Text;
use Dracodeum\Kit\Enumerations\InfoLevel as EInfoLevel;
use Dracodeum\Kit\Options\Text as TextOptions;
use Dracodeum\Kit\Utilities\Call\Exceptions\AssertionFailed as CallAssertionFailedException;

/** @see \Dracodeum\Kit\Primitives\Text */
class TextTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>Dracodeum\Kit\Interfaces\StringInstantiable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
	 * @return void
	 */
	public function testStringInstantiableInterface(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//instantiate
		$text = Text::fromString($string);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, $text->toString());
	}
	
	/**
	 * Test <code>Dracodeum\Kit\Interfaces\Cloneable</code> interface.
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Cloneable
	 * @return void
	 */
	public function testCloneableInterface(): void
	{
		//build
		$text = Text::build()
			->setString("The {{fox_count}} quick brown fox named {{fox_name}} jumps over the lazy dog.")
			->setPluralString(
				"The {{fox_count}} quick brown foxes named {{fox_name}} jump over the lazy dog."
			)
			->setString(
				"The {{fox_count}} high-speed brown vulpes named {{fox_name}} jumps over the laziest canis.",
				EInfoLevel::TECHNICAL
			)
			->setPluralString(
				"The {{fox_count}} high-speed brown vulpes named {{fox_name}} jump over the laziest canis.",
				EInfoLevel::TECHNICAL
			)
			->setPluralNumberPlaceholder('fox_count')
			->setPluralNumber(12)
			->setParameters(['fox_name' => "Cooper"])
		;
		
		//clone
		$clone = $text->clone();
		
		//assert
		$this->assertEquals($text, $clone);
	}
	
	/**
	 * Test <code>JsonSerializable</code> interface.
	 * 
	 * @see https://www.php.net/manual/en/class.jsonserializable.php
	 * @return void
	 */
	public function testJsonSerializableInterface(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame(json_encode($string), json_encode($text));
	}
	
	/**
	 * Test string.
	 * 
	 * @return void
	 */
	public function testString(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		foreach (EInfoLevel::getValues() as $info_level) {
			$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => $info_level])));
		}
	}
	
	/**
	 * Test technical string.
	 * 
	 * @return void
	 */
	public function testTechnicalString(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string, EInfoLevel::TECHNICAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame('', $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
		$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
	}
	
	/**
	 * Test internal string.
	 * 
	 * @return void
	 */
	public function testInternalString(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string, EInfoLevel::INTERNAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame('', $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame('', $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
		$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
	}
	
	/**
	 * Test end-user and technical strings.
	 * 
	 * @return void
	 */
	public function testEnduserTechnicalStrings(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		$string_tech = "The high-speed brown vulpes jumps over the laziest canis.";
		
		//build
		$text = Text::build($string)->setString($string_tech, EInfoLevel::TECHNICAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame($string_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
		$this->assertSame($string_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
	}
	
	/**
	 * Test end-user, technical and internal strings.
	 * 
	 * @return void
	 */
	public function testEnduserTechnicalInternalStrings(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		$string_tech = "The high-speed brown vulpes jumps over the laziest canis.";
		$string_intern = "The high-speed brown vulpes jumps over the laziest canis everyday at noon.";
		
		//build
		$text = Text::build($string)
			->setString($string_tech, EInfoLevel::TECHNICAL)
			->setString($string_intern, EInfoLevel::INTERNAL)
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame($string, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame($string_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
		$this->assertSame($string_intern, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
	}
	
	/**
	 * Test technical and internal strings.
	 * 
	 * @return void
	 */
	public function testTechnicalInternalStrings(): void
	{
		//initialize
		$string_tech = "The high-speed brown vulpes jumps over the laziest canis.";
		$string_intern = "The high-speed brown vulpes jumps over the laziest canis everyday at noon.";
		
		//build
		$text = Text::build($string_tech, EInfoLevel::TECHNICAL)->setString($string_intern, EInfoLevel::INTERNAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_tech, (string)$text);
		$this->assertSame($string_tech, $text->toString());
		$this->assertSame('', $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame($string_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
		$this->assertSame($string_intern, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
	}
	
	/**
	 * Test plural.
	 * 
	 * @return void
	 */
	public function testPlural(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		$string_plural = "The quick brown foxes jump over the lazy dog.";
		
		//build
		$text = Text::build($string)->setPluralString($string_plural);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		
		//assert (singular)
		foreach ([1, -1] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame($string, $text->toString());
		}
		
		//assert (plural)
		foreach ([0, 2, -2, 1.5, -1.5] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame($string_plural, $text->toString());
		}
	}
	
	/**
	 * Test plural placeholder.
	 * 
	 * @return void
	 */
	public function testPluralPlaceholder(): void
	{
		//initialize
		$string = "The {{fox_count}} quick brown fox jumps over the lazy dog.";
		$string_plural = "The {{fox_count}} quick brown foxes jump over the lazy dog.";
		
		//build
		$text = Text::build($string)->setPluralString($string_plural)->setPluralNumberPlaceholder('fox_count');
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		
		//assert (singular)
		foreach ([1, -1] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame(str_replace('{{fox_count}}', $number, $string), $text->toString());
		}
		
		//assert (plural)
		foreach ([0, 2, -2, 1.5, -1.5] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame(str_replace('{{fox_count}}', $number, $string_plural), $text->toString());
		}
	}
	
	/**
	 * Test parameter.
	 * 
	 * @return void
	 */
	public function testParameter(): void
	{
		//initialize
		$fox_name = "Cooper";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog.";
		$string_param = "The quick brown fox {$fox_name} jumps over the lazy dog.";
		
		//build
		$text = Text::build($string)->setParameter('fox_name', $fox_name);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test parameters.
	 * 
	 * @return void
	 */
	public function testParameters(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name} jumps over the lazy dog {$dog_name}.";
		
		//build
		$text = Text::build($string)->setParameters([
			'fox_name' => $fox_name,
			'dog_name' => $dog_name
		]);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test end-user and technical strings parameter.
	 * 
	 * @return void
	 */
	public function testEnduserTechnicalStringsParameter(): void
	{
		//initialize
		$fox_name = "Cooper";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog.";
		$string_tech = "The high-speed brown vulpes {{fox_name}} jumps over the laziest canis.";
		$string_param = "The quick brown fox {$fox_name} jumps over the lazy dog.";
		$string_tech_param = "The high-speed brown vulpes {$fox_name} jumps over the laziest canis.";
		
		//build
		$text = Text::build($string)
			->setString($string_tech, EInfoLevel::TECHNICAL)
			->setParameter('fox_name', $fox_name)
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
		$this->assertSame($string_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame(
			$string_tech_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL]))
		);
		$this->assertSame(
			$string_tech_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL]))
		);
	}
	
	/**
	 * Test end-user and technical strings parameters.
	 * 
	 * @return void
	 */
	public function testEnduserTechnicalStringsParameters(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_tech = "The high-speed brown vulpes {{fox_name}} jumps over the laziest canis {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name} jumps over the lazy dog {$dog_name}.";
		$string_tech_param = "The high-speed brown vulpes {$fox_name} jumps over the laziest canis {$dog_name}.";
		
		//build
		$text = Text::build($string)
			->setString($string_tech, EInfoLevel::TECHNICAL)
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
		$this->assertSame($string_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
		$this->assertSame(
			$string_tech_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL]))
		);
		$this->assertSame(
			$string_tech_param, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL]))
		);
	}
	
	/**
	 * Test end-user and technical strings parameters plural.
	 * 
	 * @return void
	 */
	public function testEnduserTechnicalStringsParametersPlural(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string = "The {{fox_count}} quick brown fox named {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_tech = "The {{fox_count}} high-speed brown vulpes named {{fox_name}} jumps over the " . 
			"laziest canis {{dog_name}}.";
		$string_param = "The {{fox_count}} quick brown fox named {$fox_name} jumps over the lazy dog {$dog_name}.";
		$string_tech_param = "The {{fox_count}} high-speed brown vulpes named {$fox_name} jumps over the " . 
			"laziest canis {$dog_name}.";
		$string_plural = "The {{fox_count}} quick brown foxes named {{fox_name}} jump over the lazy dog {{dog_name}}.";
		$string_plural_tech = "The {{fox_count}} high-speed brown vulpes named {{fox_name}} jump over the " . 
			"laziest canis {{dog_name}}.";
		$string_plural_param = "The {{fox_count}} quick brown foxes named {$fox_name} jump over the " . 
			"lazy dog {$dog_name}.";
		$string_plural_tech_param = "The {{fox_count}} high-speed brown vulpes named {$fox_name} jump over the " . 
			"laziest canis {$dog_name}.";
		
		//build
		$text = Text::build($string)
			->setPluralString($string_plural)
			->setString($string_tech, EInfoLevel::TECHNICAL)
			->setPluralString($string_plural_tech, EInfoLevel::TECHNICAL)
			->setPluralNumberPlaceholder('fox_count')
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		
		//assert (singular)
		foreach ([1, -1] as $number) {
			//initialize
			$s = str_replace('{{fox_count}}', $number, $string_param);
			$s_tech = str_replace('{{fox_count}}', $number, $string_tech_param);
			$text->setPluralNumber($number);
			
			//assert
			$this->assertSame($s, $text->toString());
			$this->assertSame($s, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
			$this->assertSame($s_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
			$this->assertSame($s_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
		}
		
		//assert (plural)
		foreach ([0, 2, -2, 1.5, -1.5] as $number) {
			//initialize
			$s = str_replace('{{fox_count}}', $number, $string_plural_param);
			$s_tech = str_replace('{{fox_count}}', $number, $string_plural_tech_param);
			$text->setPluralNumber($number);
			
			//assert
			$this->assertSame($s, $text->toString());
			$this->assertSame($s, $text->toString(TextOptions::build(['info_level' => EInfoLevel::ENDUSER])));
			$this->assertSame($s_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::TECHNICAL])));
			$this->assertSame($s_tech, $text->toString(TextOptions::build(['info_level' => EInfoLevel::INTERNAL])));
		}
	}
	
	/**
	 * Test placeholder stringifier.
	 * 
	 * @return void
	 */
	public function testPlaceholderStringifier(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$fox_name_upper = strtoupper($fox_name);
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name_upper} jumps over the lazy dog {$dog_name}.";
		
		//build
		$text = Text::build($string)
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
			->setPlaceholderStringifier(
				'fox_name', fn (mixed $value, TextOptions $text_options): string => strtoupper($value)
			)
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test placeholder stringifier expecting an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @dataProvider providePlaceholderStringifierData_AssertionFailedException
	 * 
	 * @param callable $stringifier
	 * <p>The <var>$stringifier</var> parameter to test with.</p>
	 * @return void
	 */
	public function testPlaceholderStringifier_AssertionFailedException(callable $stringifier): void
	{
		$this->expectException(CallAssertionFailedException::class);
		try {
			Text::build()->setPlaceholderStringifier('_', $stringifier);
		} catch (CallAssertionFailedException $exception) {
			$this->assertSame('stringifier', $exception->name);
			$this->assertSame($stringifier, $exception->function);
			throw $exception;
		}
	}
	
	/**
	 * Provide placeholder stringifier data for an <code>AssertionFailed</code> exception to be thrown.
	 * 
	 * @return array
	 * <p>The placeholder stringifier data for an <code>AssertionFailed</code> exception to be thrown.</p>
	 */
	public function providePlaceholderStringifierData_AssertionFailedException(): array
	{
		return [
			[function (mixed $value): string {}],
			[function (mixed $value, TextOptions $text_options) {}],
			[function (string $value, TextOptions $text_options): string {}]
		];
	}
	
	/**
	 * Test localized.
	 * 
	 * @return void
	 */
	public function testLocalized(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string);
		
		//assert
		$this->assertSame($text, $text->setAsLocalized());
	}
}
