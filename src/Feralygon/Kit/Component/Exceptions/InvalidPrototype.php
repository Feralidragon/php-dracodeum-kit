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
 * @property-read mixed $prototype <p>The prototype.</p>
 */
class InvalidPrototype extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype {{prototype}} for component {{component}}.\n" . 
			"HINT: Only the following types and formats are allowed:\n" . 
			" - an instance, class or name;\n" . 
			" - a \"class, properties\" array, with the properties given as \"name => value\" pairs;\n" . 
			" - a \"name, properties\" array, with the properties given as \"name => value\" pairs;\n" . 
			" - a set of properties, as \"name => value\" pairs.";
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
