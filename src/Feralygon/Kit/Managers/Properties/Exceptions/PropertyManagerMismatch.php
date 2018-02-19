<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * Properties manager property manager mismatch exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property manager mismatches the expected one.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 */
class PropertyManagerMismatch extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property manager mismatch for {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.\n" . 
			"HINT: The manager which a given property is set with and the one it is being added to " . 
			"must be exactly the same.";
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
