<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Base32\Exceptions\Decode;

use Dracodeum\Kit\Utilities\Base32\Exceptions\Decode as Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from the Base32 utility <code>decode</code> method whenever a given string is invalid.
 * 
 * @property-read string $string [coercive]
 * <p>The string.</p>
 * @property-read string $alphabet [coercive]
 * <p>The alphabet.</p>
 */
class InvalidString extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid string {{string}}.\n" . 
			"HINT: Only the characters {{alphabet}}, as groups of 2, 4, 5, 7 or 8 characters, " . 
			"optionally padded with equal signs (=), are allowed.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
		$this->addProperty('alphabet')
			->setAsString()
			->addEvaluator(function (&$value): bool {
				return strlen($value) === 32;
			})
		;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'alphabet') {
			return UText::commify(str_split($value), null, 'and');
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
