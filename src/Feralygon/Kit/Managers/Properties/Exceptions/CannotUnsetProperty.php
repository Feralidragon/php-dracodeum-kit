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
 * This exception is thrown from a properties manager whenever a given property is attempted to be unset.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Property $property
 * <p>The property instance.</p>
 */
class CannotUnsetProperty extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset property {{property.getName()}} in properties manager " . 
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
	}
}
