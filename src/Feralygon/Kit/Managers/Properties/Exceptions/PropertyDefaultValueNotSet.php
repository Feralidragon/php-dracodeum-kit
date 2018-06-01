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
 * This exception is thrown from a properties manager whenever no default value is set in a given property.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Property $property
 * <p>The property instance.</p>
 */
class PropertyDefaultValueNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "No default value set in property {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.\n" . 
			"HINT: Optional properties must be set with a default value.";
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
