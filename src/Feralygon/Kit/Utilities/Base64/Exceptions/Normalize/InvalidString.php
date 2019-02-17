<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Base64\Exceptions\Normalize;

use Feralygon\Kit\Utilities\Base64\Exceptions\Normalize as Exception;

/**
 * This exception is thrown from the Base64 utility <code>normalize</code> method whenever a given string is invalid.
 * 
 * @since 1.0.0
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
			"HINT: Only ASCII alphanumerical characters (0-9, a-z and A-Z), " . 
			"and also plus signs (+) and slashes (/), optionally padded with equal signs (=), " . 
			"in the case of Base64, or hyphens (-) and underscores (_) respectively, without any padding, " . 
			"in the case of URL-safe Base64, are allowed.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
	}
}
