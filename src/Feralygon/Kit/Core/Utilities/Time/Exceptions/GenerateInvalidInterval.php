<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

/**
 * Core time utility <code>generate</code> method invalid interval exception class.
 * 
 * This exception is thrown from the time utility <code>generate</code> method whenever a given interval is invalid.
 * 
 * @since 1.0.0
 * @property-read int|float $interval <p>The interval.</p>
 */
class GenerateInvalidInterval extends Generate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid interval {{interval}}.\n" . 
			"HINT: Only a value greater than 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addNumberProperty('interval', true);
	}
}
