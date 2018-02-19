<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

use Feralygon\Kit\Utilities\Call\Exception;

/**
 * Call utility invalid stack offset exception class.
 * 
 * This exception is thrown from the call utility whenever a given stack offset is invalid.
 * 
 * @since 1.0.0
 * @property-read int $offset <p>The offset.</p>
 */
class InvalidStackOffset extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid stack offset {{offset}}.\n" . 
			"HINT: Only a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('offset')->setAsInteger()->setAsRequired();
	}
}
