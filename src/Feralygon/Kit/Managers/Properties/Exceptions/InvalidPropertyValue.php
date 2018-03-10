<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * This exception is thrown from a properties manager whenever a given value is invalid for a given property.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 * @property-read mixed $value <p>The value.</p>
 */
class InvalidPropertyValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('property')->setAsStrictObject(Property::class)->setAsRequired();
		$this->addProperty('value')->setAsRequired();
	}
}
