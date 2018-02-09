<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;
use Feralygon\Kit\Core\Managers\Properties\Objects\Property;

/**
 * Core properties manager property manager mismatch exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property manager mismatches the expected one.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Managers\Properties\Objects\Property $property <p>The property instance.</p>
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
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStrictObjectProperty('property', true, Property::class);
	}
}
