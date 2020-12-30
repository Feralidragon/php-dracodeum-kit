<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Primitives;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use stdClass;

/** @see \Dracodeum\Kit\Primitives\Error */
class ErrorTest extends TestCase
{
	//Public methods
	/**
	 * Test <code>Dracodeum\Kit\Interfaces\Cloneable</code> interface.
	 * 
	 * @testdox Cloneable interface
	 * 
	 * @see \Dracodeum\Kit\Interfaces\Cloneable
	 * @return void
	 */
	public function testCloneableInterface(): void
	{
		//build
		$error = Error::build(
			name: 'InvalidValue',
			text: "The quick brown fox jumped over the lazy dog.",
			data: ['a' => 11111, 'b' => 'foobar', 'F' => new stdClass()]
		);
		
		//clone
		$clone = $error->clone();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertEquals($error, $clone);
	}
	
	/**
	 * Test <code>JsonSerializable</code> interface.
	 * 
	 * @testdox JsonSerializable interface
	 * 
	 * @see https://www.php.net/manual/en/class.jsonserializable.php
	 * @return void
	 */
	public function testJsonSerializableInterface(): void
	{
		//initialize
		$name = 'InvalidValue';
		$text = "The quick brown fox jumped over the lazy dog.";
		$data = ['a' => 11111, 'b' => 'foobar', 'F' => new stdClass()];
		$json = json_encode([
			'name' => $name,
			'text' => $text,
			'data' => $data
		]);
		
		//build
		$error = Error::build($name, $text, $data);
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertSame($json, json_encode($error));
	}
	
	/**
	 * Test name.
	 * 
	 * @testdox Name
	 * 
	 * @return void
	 */
	public function testName(): void
	{
		//initialize
		$name = 'InvalidValue';
		
		//build
		$error = Error::build();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertFalse($error->hasName());
		$this->assertNull($error->getName());
		$this->assertSame($error, $error->setName($name));
		$this->assertTrue($error->hasName());
		$this->assertSame($name, $error->getName());
	}
	
	/**
	 * Test name (build).
	 * 
	 * @testdox Name (build)
	 * 
	 * @return void
	 */
	public function testName_Build(): void
	{
		//initialize
		$name = 'InvalidValue';
		
		//build
		$error = Error::build(name: $name);
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasName());
		$this->assertSame($name, $error->getName());
	}
	
	/**
	 * Test text.
	 * 
	 * @testdox Text
	 * 
	 * @return void
	 */
	public function testText(): void
	{
		//initialize
		$string = "The quick brown fox jumped over the lazy dog.";
		$texts = [$string, Text::fromString($string)];
		
		//build
		$error = Error::build();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertFalse($error->hasText());
		$this->assertNull($error->getText());
		foreach ($texts as $text) {
			$this->assertSame($error, $error->setText($text));
			$this->assertTrue($error->hasText());
			$this->assertInstanceOf(Text::class, $error->getText());
			$this->assertSame($string, (string)$error->getText());
		}
	}
	
	/**
	 * Test text (build).
	 * 
	 * @testdox Text (build)
	 * 
	 * @return void
	 */
	public function testText_Build(): void
	{
		//initialize
		$string = "The quick brown fox jumped over the lazy dog.";
		$texts = [$string, Text::fromString($string)];
		
		//texts
		foreach ($texts as $text) {
			//build
			$error = Error::build(text: $text);
			
			//assert
			$this->assertInstanceOf(Error::class, $error);
			$this->assertTrue($error->hasText());
			$this->assertInstanceOf(Text::class, $error->getText());
			$this->assertSame($string, (string)$error->getText());
		}
	}
	
	/**
	 * Test data.
	 * 
	 * @testdox Data
	 * 
	 * @return void
	 */
	public function testData(): void
	{
		//initialize
		$data = ['a' => 11111, 'b' => 'foobar', 'F' => new stdClass()];
		
		//build
		$error = Error::build();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertFalse($error->hasData());
		$this->assertNull($error->getData());
		$this->assertSame($error, $error->setData($data));
		$this->assertTrue($error->hasData());
		$this->assertSame($data, $error->getData());
	}
	
	/**
	 * Test data (build).
	 * 
	 * @testdox Data (build)
	 * 
	 * @return void
	 */
	public function testData_Build(): void
	{
		//initialize
		$data = ['a' => 11111, 'b' => 'foobar', 'F' => new stdClass()];
		
		//build
		$error = Error::build(data: $data);
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		$this->assertTrue($error->hasData());
		$this->assertSame($data, $error->getData());
	}
}
