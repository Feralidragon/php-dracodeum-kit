<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\LazyProperties\Exception;

/**
 * Core lazy properties trait property not found exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given property is not found.
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
		return "Property {{name}} not found in object {{object}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
	}
}
