<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

use Feralygon\Kit\Utilities\Call\Exception;

/**
 * This exception is thrown from the call utility whenever a given stack limit is invalid.
 * 
 * @since 1.0.0
 * @property-read int $limit
 * <p>The limit.</p>
 */
class InvalidStackLimit extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid stack limit {{limit}}.\n" . 
			"HINT: Only null or a value greater than 0 is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('limit')->setAsInteger()->setAsRequired();
	}
}
