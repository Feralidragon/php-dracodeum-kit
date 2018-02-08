<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;
use Feralygon\Kit\Core\Managers\Properties as Manager;

/**
 * Core properties manager property manager mismatch exception class.
 * 
 * This exception is thrown from a properties manager whenever a given property manager mismatches the expected one.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 * @property-read \Feralygon\Kit\Core\Managers\Properties $property_manager <p>The property manager instance.</p>
 */
class PropertyManagerMismatch extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property manager mismatch for {{name}} in properties manager with owner {{manager.getOwner()}}.\n" . 
			"HINT: The manager a given property is set with and is being added to must be exactly the same.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
		$this->addStrictObjectProperty('property_manager', true, Manager::class);
	}
}
