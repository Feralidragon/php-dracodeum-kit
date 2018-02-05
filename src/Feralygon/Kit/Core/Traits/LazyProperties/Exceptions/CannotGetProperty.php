<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\LazyProperties\Exception;

/**
 * Core lazy properties trait cannot get property exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given property 
 * with a given name is attempted to be retrieved.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 */
class CannotGetProperty extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get property {{name}} from object {{object}}.";
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
