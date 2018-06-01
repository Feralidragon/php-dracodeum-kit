<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Property\Exceptions;

use Feralygon\Kit\Managers\Properties\Property\Exception;

/**
 * This exception is thrown from a property whenever a given value is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{property.getName()}} in properties manager " . 
			"with owner {{property.getManager().getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
	}
}
