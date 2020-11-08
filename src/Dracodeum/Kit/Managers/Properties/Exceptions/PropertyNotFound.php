<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Properties\Exceptions;

use Dracodeum\Kit\Managers\Properties\Exception;

/**
 * @property-read string $name
 * <p>The name.</p>
 */
class PropertyNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Property {{name}} not found in manager with owner {{manager.getOwner()}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString();
	}
}
