<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

use Feralygon\Kit\Core\Utilities\Data\Exception;

/**
 * Core data utility invalid depth exception class.
 * 
 * This exception is thrown from the data utility whenever a given depth is invalid.
 * 
 * @since 1.0.0
 * @property-read int $depth <p>The depth.</p>
 */
class InvalidDepth extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid depth {{depth}}.\n" . 
			"HINT: Only null or a value greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('depth')->setAsInteger()->setAsRequired();
	}
}
