<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Base64\Exceptions;

/**
 * Core Base64 utility <code>decode</code> method invalid string exception class.
 * 
 * This exception is thrown from the Base64 utility <code>decode</code> method whenever a given string is invalid.
 * 
 * @since 1.0.0
 * @property-read string $string <p>The string.</p>
 * @property-read bool $url_safe [default = false] <p>URL-safe decoding.</p>
 */
class DecodeInvalidString extends Decode
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//hint message
		$hint_message = $this->is('url_safe')
			? "Only ASCII alphanumeric (0-9, a-z and A-Z), hyphen (-) and underscore (_) characters are allowed."
			: "Only ASCII alphanumeric (0-9, a-z and A-Z), plus sign (+) and slash (/) characters, " . 
				"optionally suffixed with equal signs (=), are allowed.";
		
		//return
		return "Invalid string {{string}}.\n" . 
			"HINT: {$hint_message}";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//properties
		$this->addStringProperty('string', true);
		$this->addBooleanProperty('url_safe');
		
		//defaults
		$this->setPropertyDefaultValue('url_safe', false);
	}
}
