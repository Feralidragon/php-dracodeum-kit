<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Exceptions;

/**
 * Core text utility <code>truncate</code> method invalid length exception class.
 * 
 * This exception is thrown from the text utility <code>truncate</code> method whenever a given length is invalid.
 * 
 * @since 1.0.0
 * @property-read int $length <p>The length.</p>
 */
class TruncateInvalidLength extends Truncate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid length {{length}}.\n" . 
			"HINT: Only a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addIntegerProperty('length', true);
	}
}
