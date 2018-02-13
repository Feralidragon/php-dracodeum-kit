<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;
use Feralygon\Kit\Core\Managers\Properties\Objects\Property;

/**
 * Core properties manager property not initialized exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property has not been initialized yet.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Managers\Properties\Objects\Property $property <p>The property instance.</p>
 */
class PropertyNotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property {{property.getName()}} has not been initialized yet in properties manager " . 
			"with owner {{manager.getOwner()}}.\n" . 
			"HINT: Properties must be initialized first with a value, default value, accessors or through binding.";
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
