<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Base64\Exceptions\Normalize;

use Dracodeum\Kit\Utilities\Base64\Exceptions\Normalize as Exception;

/**
 * @property-read string $string
 * <p>The string.</p>
 */
class InvalidString extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid string {{string}}.\n" . 
			"HINT: Only ASCII alphanumeric characters (0-9, a-z and A-Z), " . 
			"and also plus signs (+) and slashes (/), optionally padded with equal signs (=), " . 
			"in the case of Base64, or hyphens (-) and underscores (_) respectively, without any padding, " . 
			"in the case of URL-safe Base64, as groups of 2 to 4 characters, are allowed.";
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
	}
}
