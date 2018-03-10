<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;

/**
 * This exception is thrown from a properties manager whenever a given property is not found.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 */
class PropertyNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property {{name}} not found in properties manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
	}
}
