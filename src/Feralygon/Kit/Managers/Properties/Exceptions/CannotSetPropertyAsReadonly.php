<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * Properties manager cannot set property as read-only exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property is attempted to be set as read-only.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 */
class CannotSetPropertyAsReadonly extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set property {{property.getName()}}, with mode {{property.getMode()}}, as read-only " . 
			"in properties manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
