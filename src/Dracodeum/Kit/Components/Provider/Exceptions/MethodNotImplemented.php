<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Provider\Exceptions;

use Dracodeum\Kit\Components\Provider\Exception;

/**
 * This exception is thrown from a provider whenever a given method is not implemented.
 * 
 * @property-read string $name
 * <p>The name.<br>
 * It cannot be empty.</p>
 */
class MethodNotImplemented extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Method {{name}} not implemented in provider {{component}} (with prototype {{prototype}}).";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString(true);
	}
}
