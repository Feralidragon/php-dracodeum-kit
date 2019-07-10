<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\DebugInfo\Info\Exceptions;

use Feralygon\Kit\Traits\DebugInfo\Info\Exception;

/**
 * This exception is thrown from a debug info instance whenever a given property is not found.
 * 
 * @since 1.0.0
 * @property-read string $name [coercive]
 * <p>The name.</p>
 */
class PropertyNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Property {{name}} not found in debug info {{info}}.";
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
