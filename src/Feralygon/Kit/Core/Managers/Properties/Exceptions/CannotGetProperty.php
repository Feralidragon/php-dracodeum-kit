<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;
use Feralygon\Kit\Core\Managers\Properties\Objects\Property;

/**
 * Core properties manager cannot get property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property is attempted to be retrieved.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 */
class CannotGetProperty extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get property {{property.getName()}} from properties manager with owner {{manager.getOwner()}}.";
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
