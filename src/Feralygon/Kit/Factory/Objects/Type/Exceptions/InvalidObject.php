<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory\Objects\Type\Exceptions;

use Feralygon\Kit\Factory\Objects\Type\Exception;

/**
 * Factory type object invalid object exception class.
 * 
 * This exception is thrown from a type object whenever a given object is invalid.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 */
class InvalidObject extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid object {{object}} for type {{type.getName()}} from builder {{type.getBuilder()}} " . 
			"in factory {{type.getFactory()}}.";
		if ($this->get('type')->hasClass()) {
			$message .= "\n" . 
				"HINT: Only an object class or subclass of {{type.getClass()}} is allowed for this type.";
		}
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('object')->setAsObject()->setAsRequired();
	}
}
