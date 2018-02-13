<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;

/**
 * Core properties manager invalid owner exception class.
 * 
 * This exception is thrown from a properties manager whenever a given owner is invalid.
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
		return "Invalid owner {{owner}} for properties manager {{manager}}.\n" . 
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
