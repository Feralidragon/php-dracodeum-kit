<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * This exception is thrown from a properties manager whenever a given property is attempted to be got.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property
 * <p>The property instance.</p>
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
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
	}
}
