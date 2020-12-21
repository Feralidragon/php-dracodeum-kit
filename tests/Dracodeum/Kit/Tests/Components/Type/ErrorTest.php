<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Tests\Components\Type;

use PHPUnit\Framework\TestCase;
use Dracodeum\Kit\Components\Type\Error;
use Dracodeum\Kit\Primitives\Text;

/** @see \Dracodeum\Kit\Components\Type\Error */
class ErrorTest extends TestCase
{
	//Public methods
	/**
	 * Test text.
	 * 
	 * @return void
	 */
	public function testText(): void
	{
		//initialize
		$string = "The quick brown fox jumped over the lazy dog.";
		$texts = [$string, Text::fromString($string)];
		
		//instantiate
		$error = new Error();
		
		//assert
		$this->assertFalse($error->hasText());
		$this->assertNull($error->getText());
		foreach ($texts as $text) {
			$this->assertSame($error, $error->setText($text));
			$this->assertTrue($error->hasText());
			$this->assertInstanceOf(Text::class, $error->getText());
			$this->assertSame($string, (string)$error->getText());
		}
	}
}
