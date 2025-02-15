<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Properties\Property\Exceptions;

use Dracodeum\Kit\Managers\Properties\Property\Exception;

/**
 * @property-read mixed $value
 * <p>The value.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid value {{value}} for property {{property.getName()}} in manager " . 
			"with owner {{property.getManager().getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
	}
}
