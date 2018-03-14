<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Hash\Exceptions;

use Feralygon\Kit\Utilities\Hash\Exception;

/**
 * This exception is thrown from the hash utility whenever a given number of bits is invalid.
 * 
 * @since 1.0.0
 * @property-read int $bits
 * <p>The number of bits.</p>
 */
class InvalidBits extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid bits {{bits}}.\n" . 
			"HINT: Only a multiple of 8 and a value greater than 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('bits')->setAsInteger()->setAsRequired();
	}
}
