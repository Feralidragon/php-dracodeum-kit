<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Exceptions;

use Feralygon\Kit\Component\Exception;

/**
 * This exception is thrown from a component whenever a given prototype is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $prototype
 * <p>The prototype.</p>
 */
class InvalidPrototype extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype {{prototype}} for component {{component}}.\n" . 
			"HINT: Only an instance, class or name is allowed.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('prototype')->setAsRequired();
	}
}
