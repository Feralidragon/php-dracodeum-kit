<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
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
	 * Test `StringInstantiable` interface.
	 * 
	 * @testdox StringInstantiable interface
	 * 
	 * @see \Dracodeum\Kit\Interfaces\StringInstantiable
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
	 * Test `Cloneable` interface.
	 * 
	 * @testdox Cloneable interface
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Cloneable
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
		$this->assertInstanceOf(Text::class, $clone);
		$this->assertEquals($text, $clone);
		$this->assertNotSame($text, $clone);
	}
	
	/**
	 * Test `JsonSerializable` interface.
	 * 
	 * @testdox JsonSerializable interface
	 * 
	 * @see https://www.php.net/manual/en/class.jsonserializable.php
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
	 * @testdox String
	 */
	public function testString(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		foreach (EInfoLevel::getValues() as $info_level) {
			$this->assertSame($string, $text->toString(['info_level' => $info_level]));
		}
	}
	
	/**
	 * Test string (technical).
	 * 
	 * @testdox String (technical)
	 */
	public function testString_Technical(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string, EInfoLevel::TECHNICAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertFalse($text->hasString());
		$this->assertNull($text->getString());
		$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertSame($string, $text->getString(EInfoLevel::TECHNICAL));
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame('', $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test string (internal).
	 * 
	 * @testdox String (internal)
	 */
	public function testString_Internal(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string, EInfoLevel::INTERNAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertFalse($text->hasString());
		$this->assertNull($text->getString());
		$this->assertFalse($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertNull($text->getString(EInfoLevel::TECHNICAL));
		$this->assertTrue($text->hasString(EInfoLevel::INTERNAL));
		$this->assertSame($string, $text->getString(EInfoLevel::INTERNAL));
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame('', $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame('', $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test strings (end-user and technical).
	 * 
	 * @testdox String (end-user and technical)
	 */
	public function testStrings_Enduser_Technical(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		$string_tech = "The high-speed brown vulpes jumps over the laziest canis.";
		
		//build
		$text = Text::build($string)->setString($string_tech, EInfoLevel::TECHNICAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
		$this->assertFalse($text->hasString(EInfoLevel::INTERNAL));
		$this->assertNull($text->getString(EInfoLevel::INTERNAL));
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame($string, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string_tech, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string_tech, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test strings (end-user, technical and internal).
	 * 
	 * @testdox String (end-user, technical and internal)
	 */
	public function testStrings_Enduser_Technical_Internal(): void
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
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
		$this->assertTrue($text->hasString(EInfoLevel::INTERNAL));
		$this->assertSame($string_intern, $text->getString(EInfoLevel::INTERNAL));
		$this->assertSame($string, (string)$text);
		$this->assertSame($string, $text->toString());
		$this->assertSame($string, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string_tech, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string_intern, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test strings (technical and internal).
	 * 
	 * @testdox Strings (technical and internal)
	 */
	public function testStrings_Technical_Internal(): void
	{
		//initialize
		$string_tech = "The high-speed brown vulpes jumps over the laziest canis.";
		$string_intern = "The high-speed brown vulpes jumps over the laziest canis everyday at noon.";
		
		//build
		$text = Text::build($string_tech, EInfoLevel::TECHNICAL)->setString($string_intern, EInfoLevel::INTERNAL);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertFalse($text->hasString());
		$this->assertNull($text->getString());
		$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
		$this->assertTrue($text->hasString(EInfoLevel::INTERNAL));
		$this->assertSame($string_intern, $text->getString(EInfoLevel::INTERNAL));
		$this->assertSame($string_tech, (string)$text);
		$this->assertSame($string_tech, $text->toString());
		$this->assertSame('', $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string_tech, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string_intern, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test plural string.
	 * 
	 * @testdox Plural string
	 */
	public function testPluralString(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		$string_plural = "The quick brown foxes jump over the lazy dog.";
		
		//build
		$text = Text::build($string)->setPluralString($string_plural);
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertTrue($text->hasPluralString());
		$this->assertSame($string_plural, $text->getPluralString());
		$this->assertSame(1.0, $text->getPluralNumber());
		$this->assertFalse($text->hasPluralNumberPlaceholder());
		$this->assertNull($text->getPluralNumberPlaceholder());
		
		//assert (singular)
		foreach ([1, -1] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame((float)$number, $text->getPluralNumber());
			$this->assertSame($string, $text->toString());
		}
		
		//assert (plural)
		foreach ([0, 2, -2, 1.5, -1.5] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame((float)$number, $text->getPluralNumber());
			$this->assertSame($string_plural, $text->toString());
		}
	}
	
	/**
	 * Test plural string (placeholder).
	 * 
	 * @testdox Plural string (placeholder)
	 */
	public function testPluralString_Placeholder(): void
	{
		//initialize
		$string = "The {{fox_count}} quick brown fox jumps over the lazy dog.";
		$string_plural = "The {{fox_count}} quick brown foxes jump over the lazy dog.";
		
		//build
		$text = Text::build($string)->setPluralString($string_plural)->setPluralNumberPlaceholder('fox_count');
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertTrue($text->hasPluralString());
		$this->assertSame($string_plural, $text->getPluralString());
		$this->assertSame(1.0, $text->getPluralNumber());
		$this->assertTrue($text->hasPluralNumberPlaceholder());
		$this->assertSame('fox_count', $text->getPluralNumberPlaceholder());
		
		//assert (singular)
		foreach ([1, -1] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame((float)$number, $text->getPluralNumber());
			$this->assertSame(str_replace('{{fox_count}}', $number, $string), $text->toString());
		}
		
		//assert (plural)
		foreach ([0, 2, -2, 1.5, -1.5] as $number) {
			$this->assertSame($text, $text->setPluralNumber($number));
			$this->assertSame((float)$number, $text->getPluralNumber());
			$this->assertSame(str_replace('{{fox_count}}', $number, $string_plural), $text->toString());
		}
	}
	
	/**
	 * Test parameter.
	 * 
	 * @testdox Parameter
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
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test parameters.
	 * 
	 * @testdox Parameters
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
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test object.
	 * 
	 * @testdox Object
	 */
	public function testObject(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name} jumps over the lazy dog {$dog_name}.";
		
		//build
		$text = Text::build($string)->setObject(new class ($fox_name, $dog_name) {
			public function __construct(public string $fox_name, public string $dog_name) {}
		});
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test strings (end-user and technical, with parameter).
	 * 
	 * @testdox Strings (end-user and technical, with parameter)
	 */
	public function testStrings_Enduser_Technical_Parameter(): void
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
		$this->assertTrue($text->hasString());
		$this->assertSame($string, $text->getString());
		$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
		$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
		$this->assertSame($string_param, $text->toString());
		$this->assertSame($string_param, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string_tech_param, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string_tech_param, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test strings (end-user and technical, with parameters and object).
	 * 
	 * @testdox Strings (end-user and technical, with parameters and object)
	 */
	public function testStrings_Enduser_Technical_Parameters_Object(): void
	{
		//initialize
		$p_fox_name = "Cooper";
		$p_dog_name = "Murphy";
		$o_fox_name = "Marley";
		$o_dog_name = "Oliver";
		$parameter_sets = [
			[],
			['fox_name' => $p_fox_name],
			['dog_name' => $p_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $p_dog_name]
		];
		$object = new class ($o_fox_name, $o_dog_name) {
			public function __construct(public string $fox_name, public string $dog_name) {}
		};
		$values = [
			['fox_name' => $o_fox_name, 'dog_name' => $o_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $o_dog_name],
			['fox_name' => $o_fox_name, 'dog_name' => $p_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $p_dog_name]
		];
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_tech = "The high-speed brown vulpes {{fox_name}} jumps over the laziest canis {{dog_name}}.";
		
		//iterate
		foreach ($parameter_sets as $i => $parameters) {
			//initialize
			$string_param = "The quick brown fox {$values[$i]['fox_name']} jumps over " . 
				"the lazy dog {$values[$i]['dog_name']}.";
			$string_tech_param = "The high-speed brown vulpes {$values[$i]['fox_name']} jumps over " . 
				"the laziest canis {$values[$i]['dog_name']}.";
			
			//build
			$text = Text::build($string)
				->setString($string_tech, EInfoLevel::TECHNICAL)
				->setParameters($parameters)
				->setObject($object)
			;
			
			//assert
			$this->assertInstanceOf(Text::class, $text);
			$this->assertTrue($text->hasString());
			$this->assertSame($string, $text->getString());
			$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
			$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
			$this->assertFalse($text->hasString(EInfoLevel::INTERNAL));
			$this->assertNull($text->getString(EInfoLevel::INTERNAL));
			$this->assertSame($string_param, $text->toString());
			$this->assertSame($string_param, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
			$this->assertSame($string_tech_param, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
			$this->assertSame($string_tech_param, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
		}
	}
	
	/**
	 * Test plural strings (end-user and technical, with parameters and object).
	 * 
	 * @testdox Plural strings (end-user and technical, with parameters and object)
	 */
	public function testPluralStrings_Enduser_Technical_Parameters_Object(): void
	{
		//initialize
		$p_fox_name = "Cooper";
		$p_dog_name = "Murphy";
		$o_fox_name = "Marley";
		$o_dog_name = "Oliver";
		$parameter_sets = [
			[],
			['fox_name' => $p_fox_name],
			['dog_name' => $p_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $p_dog_name]
		];
		$object = new class ($o_fox_name, $o_dog_name) {
			public function __construct(public string $fox_name, public string $dog_name) {}
		};
		$values = [
			['fox_name' => $o_fox_name, 'dog_name' => $o_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $o_dog_name],
			['fox_name' => $o_fox_name, 'dog_name' => $p_dog_name],
			['fox_name' => $p_fox_name, 'dog_name' => $p_dog_name]
		];
		$string = "The {{fox_count}} quick brown fox named {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_tech = "The {{fox_count}} high-speed brown vulpes named {{fox_name}} jumps over " . 
			"the laziest canis {{dog_name}}.";
		$string_plural = "The {{fox_count}} quick brown foxes named {{fox_name}} jump over the lazy dog {{dog_name}}.";
		$string_plural_tech = "The {{fox_count}} high-speed brown vulpeses named {{fox_name}} jump over " . 
			"the laziest canis {{dog_name}}.";
		
		//iterate
		foreach ($parameter_sets as $i => $parameters) {
			//initialize
			$string_param = "The {{fox_count}} quick brown fox named {$values[$i]['fox_name']} " . 
				"jumps over the lazy dog {$values[$i]['dog_name']}.";
			$string_tech_param = "The {{fox_count}} high-speed brown vulpes named {$values[$i]['fox_name']} " . 
				"jumps over the laziest canis {$values[$i]['dog_name']}.";
			$string_plural_param = "The {{fox_count}} quick brown foxes named {$values[$i]['fox_name']} " . 
				"jump over the lazy dog {$values[$i]['dog_name']}.";
			$string_plural_tech_param = "The {{fox_count}} high-speed brown vulpeses named {$values[$i]['fox_name']} " .
				"jump over the laziest canis {$values[$i]['dog_name']}.";
			
			//build
			$text = Text::build($string)
				->setPluralString($string_plural)
				->setString($string_tech, EInfoLevel::TECHNICAL)
				->setPluralString($string_plural_tech, EInfoLevel::TECHNICAL)
				->setPluralNumberPlaceholder('fox_count')
				->setParameters($parameters)
				->setObject($object)
			;
			
			//assert
			$this->assertInstanceOf(Text::class, $text);
			$this->assertTrue($text->hasString());
			$this->assertSame($string, $text->getString());
			$this->assertTrue($text->hasString(EInfoLevel::TECHNICAL));
			$this->assertSame($string_tech, $text->getString(EInfoLevel::TECHNICAL));
			$this->assertFalse($text->hasString(EInfoLevel::INTERNAL));
			$this->assertNull($text->getString(EInfoLevel::INTERNAL));
			$this->assertTrue($text->hasPluralString());
			$this->assertSame($string_plural, $text->getPluralString());
			$this->assertTrue($text->hasPluralString(EInfoLevel::TECHNICAL));
			$this->assertSame($string_plural_tech, $text->getPluralString(EInfoLevel::TECHNICAL));
			$this->assertFalse($text->hasPluralString(EInfoLevel::INTERNAL));
			$this->assertNull($text->getPluralString(EInfoLevel::INTERNAL));
			$this->assertSame(1.0, $text->getPluralNumber());
			$this->assertTrue($text->hasPluralNumberPlaceholder());
			$this->assertSame('fox_count', $text->getPluralNumberPlaceholder());
			
			//assert (singular)
			foreach ([1, -1] as $number) {
				//initialize
				$s = str_replace('{{fox_count}}', $number, $string_param);
				$s_tech = str_replace('{{fox_count}}', $number, $string_tech_param);
				$text->setPluralNumber($number);
				
				//assert
				$this->assertSame((float)$number, $text->getPluralNumber());
				$this->assertSame($s, $text->toString());
				$this->assertSame($s, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
				$this->assertSame($s_tech, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
				$this->assertSame($s_tech, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
			}
			
			//assert (plural)
			foreach ([0, 2, -2, 1.5, -1.5] as $number) {
				//initialize
				$s = str_replace('{{fox_count}}', $number, $string_plural_param);
				$s_tech = str_replace('{{fox_count}}', $number, $string_plural_tech_param);
				$text->setPluralNumber($number);
				
				//assert
				$this->assertSame((float)$number, $text->getPluralNumber());
				$this->assertSame($s, $text->toString());
				$this->assertSame($s, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
				$this->assertSame($s_tech, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
				$this->assertSame($s_tech, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
			}
		}
	}
	
	/**
	 * Test stringifier.
	 * 
	 * @testdox Stringifier
	 */
	public function testStringifier(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$fox_name_upper = strtoupper($fox_name);
		$dog_name_upper = strtoupper($dog_name);
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name_upper} jumps over the lazy dog {$dog_name_upper}.";
		
		//build
		$text = Text::build($string)
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
			->setStringifier(fn (mixed $value, TextOptions $text_options): string => strtoupper($value))
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test stringifier expecting an `AssertionFailed` exception to be thrown.
	 * 
	 * @testdox Stringifier AssertionFailed exception
	 * @dataProvider provideStringifierData_AssertionFailedException
	 * 
	 * @param callable $stringifier
	 * The stringifier to test with.
	 */
	public function testStringifier_AssertionFailedException(callable $stringifier): void
	{
		$this->expectException(CallAssertionFailedException::class);
		try {
			Text::build()->setStringifier($stringifier);
		} catch (CallAssertionFailedException $exception) {
			$this->assertSame('stringifier', $exception->name);
			$this->assertSame($stringifier, $exception->function);
			throw $exception;
		}
	}
	
	/**
	 * Test placeholder as quoted.
	 * 
	 * @testdox Placeholder as quoted
	 */
	public function testPlaceholderAsQuoted(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox \"{$fox_name}\" jumps over the lazy dog {$dog_name}.";
		$string_param_enduser = "The quick brown fox \u{201c}{$fox_name}\u{201d} jumps over the lazy dog {$dog_name}.";
		
		//build
		$text = Text::build($string)
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
			->setPlaceholderAsQuoted('fox_name')
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param_enduser, $text->toString(['info_level' => EInfoLevel::ENDUSER]));
		$this->assertSame($string_param, $text->toString(['info_level' => EInfoLevel::TECHNICAL]));
		$this->assertSame($string_param, $text->toString(['info_level' => EInfoLevel::INTERNAL]));
	}
	
	/**
	 * Test placeholder stringifier.
	 * 
	 * @testdox Placeholder stringifier
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
	 * Test placeholder stringifier expecting an `AssertionFailed` exception to be thrown.
	 * 
	 * @testdox Placeholder stringifier AssertionFailed exception
	 * @dataProvider providePlaceholderStringifierData_AssertionFailedException
	 * 
	 * @param callable $stringifier
	 * The stringifier to test with.
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
	 * Test stringifier (with placeholder stringifier).
	 * 
	 * @testdox Stringifier (with placeholder stringifier)
	 */
	public function testStringifier_PlaceholderStringifier(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$fox_name_lower = strtolower($fox_name);
		$dog_name_upper = strtoupper($dog_name);
		$string = "The quick brown fox {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string_param = "The quick brown fox {$fox_name_lower} jumps over the lazy dog {$dog_name_upper}.";
		
		//build
		$text = Text::build($string)
			->setParameters([
				'fox_name' => $fox_name,
				'dog_name' => $dog_name
			])
			->setStringifier(fn (mixed $value, TextOptions $text_options): string => strtoupper($value))
			->setPlaceholderStringifier(
				'fox_name', fn (mixed $value, TextOptions $text_options): string => strtolower($value)
			)
		;
		
		//assert
		$this->assertInstanceOf(Text::class, $text);
		$this->assertSame($string_param, $text->toString());
	}
	
	/**
	 * Test localized.
	 * 
	 * @testdox Localized
	 */
	public function testLocalized(): void
	{
		//initialize
		$string = "The quick brown fox jumps over the lazy dog.";
		
		//build
		$text = Text::build($string);
		
		//assert
		$this->assertFalse($text->isLocalized());
		$this->assertSame($text, $text->setAsLocalized());
		$this->assertTrue($text->isLocalized());
	}
	
	/**
	 * Test texts (append).
	 * 
	 * @testdox Texts (append)
	 */
	public function testTexts_Append(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string_main = "The following is a set of appended test sentences.";
		$string1 = "The quick brown fox jumps over the lazy dog.";
		$string2 = "The quick brown fox named {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string2_param = "The quick brown fox named {$fox_name} jumps over the lazy dog {$dog_name}.";
		$string1_2_param = "{$string1}\n{$string2_param}";
		$string_main_1_2_param = "{$string_main}\n{$string1}\n{$string2_param}";
		
		//build
		$text = Text::build();
		
		//assert
		$this->assertFalse($text->hasTexts());
		$this->assertSame([], $text->getTexts());
		$this->assertSame('', $text->toString());
		
		//assert (0)
		$this->assertSame($text, $text->appendText(''));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(1, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[0]);
		$this->assertSame('', $text->getTexts()[0]->toString());
		$this->assertSame('', $text->toString());
		
		//assert (1)
		$this->assertSame($text, $text->appendText($string1));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(2, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[1]);
		$this->assertSame($string1, $text->getTexts()[1]->toString());
		$this->assertSame($string1, $text->toString());
		
		//text (2)
		$text2 = Text::build($string2)->setParameters(['fox_name' => $fox_name, 'dog_name' => $dog_name]);
		
		//assert (2)
		$this->assertSame($text, $text->appendText($text2));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(3, count($text->getTexts()));
		$this->assertSame($text2, $text->getTexts()[2]);
		$this->assertSame($string2_param, $text->getTexts()[2]->toString());
		$this->assertSame($string1_2_param, $text->toString());
		
		//assert (3)
		$this->assertSame($text, $text->appendText(''));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(4, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[3]);
		$this->assertSame('', $text->getTexts()[3]->toString());
		$this->assertSame($string1_2_param, $text->toString());
		
		//assert (4)
		$this->assertSame($text, $text->setString($string_main));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(4, count($text->getTexts()));
		$this->assertSame($string_main_1_2_param, $text->toString());
	}
	
	/**
	 * Test texts (prepend).
	 * 
	 * @testdox Texts (prepend)
	 */
	public function testTexts_Prepend(): void
	{
		//initialize
		$fox_name = "Cooper";
		$dog_name = "Murphy";
		$string_main = "The following is a set of prepended test sentences.";
		$string1 = "The quick brown fox jumps over the lazy dog.";
		$string2 = "The quick brown fox named {{fox_name}} jumps over the lazy dog {{dog_name}}.";
		$string2_param = "The quick brown fox named {$fox_name} jumps over the lazy dog {$dog_name}.";
		$string2_param_1 = "{$string2_param}\n{$string1}";
		$string_main_2_param_1 = "{$string_main}\n{$string2_param}\n{$string1}";
		
		//build
		$text = Text::build();
		
		//assert
		$this->assertFalse($text->hasTexts());
		$this->assertSame([], $text->getTexts());
		$this->assertSame('', $text->toString());
		
		//assert (0)
		$this->assertSame($text, $text->prependText(''));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(1, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[0]);
		$this->assertSame('', $text->getTexts()[0]->toString());
		$this->assertSame('', $text->toString());
		
		//assert (1)
		$this->assertSame($text, $text->prependText($string1));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(2, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[0]);
		$this->assertSame($string1, $text->getTexts()[0]->toString());
		$this->assertSame($string1, $text->toString());
		
		//text (2)
		$text2 = Text::build($string2)->setParameters(['fox_name' => $fox_name, 'dog_name' => $dog_name]);
		
		//assert (2)
		$this->assertSame($text, $text->prependText($text2));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(3, count($text->getTexts()));
		$this->assertSame($text2, $text->getTexts()[0]);
		$this->assertSame($string2_param, $text->getTexts()[0]->toString());
		$this->assertSame($string2_param_1, $text->toString());
		
		//assert (3)
		$this->assertSame($text, $text->prependText(''));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(4, count($text->getTexts()));
		$this->assertInstanceOf(Text::class, $text->getTexts()[0]);
		$this->assertSame('', $text->getTexts()[0]->toString());
		$this->assertSame($string2_param_1, $text->toString());
		
		//assert (4)
		$this->assertSame($text, $text->setString($string_main));
		$this->assertTrue($text->hasTexts());
		$this->assertSame(4, count($text->getTexts()));
		$this->assertSame($string_main_2_param_1, $text->toString());
	}
	
	/**
	 * Test texts strings stringifier.
	 * 
	 * @testdox Texts strings stringifier
	 */
	public function testTextsStringsStringifier(): void
	{
		//initialize
		$string_main = "The following is a set of test sentences:";
		$string1 = "The quick brown fox jumps over the lazy dog.";
		$string2 = "The quick brown dog jumps over the lazy fox.";
		$string3 = "The slow red dog crawls under the quick fox.";
		$string_main_1_2_3 = "{$string_main}\n1) {$string1}; 2) {$string2}; 3) {$string3}";
		
		//build
		$text = Text::build($string_main)
			->appendText($string1)
			->appendText('')
			->appendText($string2)
			->appendText($string3)
			->setTextsStringsStringifier(function (array $strings, TextOptions $text_options): string {
				foreach ($strings as $i => &$string) {
					$string = ($i + 1) . ") {$string}";
				}
				unset($string);
				return implode('; ', $strings);
			})
		;
		
		//assert
		$this->assertSame($string_main_1_2_3, $text->toString());
	}
	
	
	
	//Public static methods
	/**
	 * Provide stringifier data for an `AssertionFailed` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public static function provideStringifierData_AssertionFailedException(): array
	{
		return [
			[function (mixed $value): string {}],
			[function (mixed $value, TextOptions $text_options) {}],
			[function (string $value, TextOptions $text_options): string {}]
		];
	}
	
	/**
	 * Provide placeholder stringifier data for an `AssertionFailed` exception to be thrown.
	 * 
	 * @return array
	 * The data.
	 */
	public static function providePlaceholderStringifierData_AssertionFailedException(): array
	{
		return [
			[function (mixed $value): string {}],
			[function (mixed $value, TextOptions $text_options) {}],
			[function (string $value, TextOptions $text_options): string {}]
		];
	}
}
