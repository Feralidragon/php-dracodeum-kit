<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\{
	Property,
	Exception
};

/**
 * This exception is thrown from a properties manager whenever a given value is invalid for a given property.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Property $property
 * <p>The property instance.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
class InvalidPropertyValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{property.getName()}} in manager " . 
			"with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('property')->setAsStrictObject(Property::class);
		$this->addProperty('value');
	}
}
