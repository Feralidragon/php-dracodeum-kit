<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Readonly\Exceptions;

use Feralygon\Kit\Managers\Readonly\Exception;

/**
 * Read-only manager invalid owner exception class.
 * 
 * This exception is thrown from a read-only manager whenever a given owner is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $owner <p>The owner.</p>
 */
class InvalidOwner extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid owner {{owner}} for read-only manager {{manager}}.\n" . 
			"HINT: Only an object is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('owner')->setAsRequired();
	}
}
