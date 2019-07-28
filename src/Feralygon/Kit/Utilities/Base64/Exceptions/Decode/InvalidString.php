<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Base64\Exceptions\Decode;

use Feralygon\Kit\Utilities\Base64\Exceptions\Decode as Exception;

/**
 * This exception is thrown from the Base64 utility <code>decode</code> method whenever a given string is invalid.
 * 
 * @property-read string $string [coercive]
 * <p>The string.</p>
 * @property-read bool $url_safe [coercive] [default = false]
 * <p>URL-safe decoding.</p>
 */
class InvalidString extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//hint message
		$hint_message = $this->is('url_safe')
			? "Only ASCII alphanumeric (0-9, a-z and A-Z), hyphen (-) and underscore (_) characters, " . 
				"as groups of 2 to 4 characters, are allowed."
			: "Only ASCII alphanumeric (0-9, a-z and A-Z), plus sign (+) and slash (/) characters, " . 
				"optionally padded with equal signs (=), as groups of 2 to 4 characters, are allowed.";
		
		//return
		return "Invalid string {{string}}.\n" . 
			"HINT: {$hint_message}";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('string')->setAsString();
		$this->addProperty('url_safe')->setAsBoolean()->setDefaultValue(false);
	}
}
