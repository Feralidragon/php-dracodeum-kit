<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Primitives;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Primitives\{
	Error,
	Text
};
use Dracodeum\Kit\Enums\Info\Level as EInfoLevel;
use Error as PhpError;
use Exception;
use stdClass;

/** @covers \Dracodeum\Kit\Primitives\Error */
class ErrorTest extends TestCase
{
	//Public methods
	/**
	 * Test name.
	 * 
	 * @testdox Name
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
	 * Test throwable.
	 * 
	 * @testdox Throwable
	 */
	public function testThrowable(): void
	{
		foreach ([new PhpError, new Exception] as $throwable) {
			//build
			$error = Error::build();
			
			//assert
			$this->assertInstanceOf(Error::class, $error);
			$this->assertFalse($error->hasThrowable());
			$this->assertNull($error->getThrowable());
			$this->assertSame($error, $error->setThrowable($throwable));
			$this->assertTrue($error->hasThrowable());
			$this->assertSame($throwable, $error->getThrowable());
		}	
	}
	
	/**
	 * Test data.
	 * 
	 * @testdox Data
	 */
	public function testData(): void
	{
		//initialize
		$data = ['a' => 11111, 'b' => 'foobar', 'F' => new stdClass];
		
		//build
		$error = Error::build();
		
		//assert
		$this->assertInstanceOf(Error::class, $error);
		foreach (EInfoLevel::cases() as $info_level) {
			$this->assertFalse($error->hasData($info_level));
			$this->assertNull($error->getData($info_level));
			$this->assertSame($error, $error->setData($data, $info_level));
			$this->assertTrue($error->hasData($info_level));
			$this->assertSame($data, $error->getData($info_level));
		}
	}
}
