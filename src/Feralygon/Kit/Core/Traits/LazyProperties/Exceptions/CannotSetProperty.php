<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\LazyProperties\Exception;

/**
 * Core lazy properties trait cannot set property exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given property 
 * with a given name is attempted to be set.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 */
class CannotSetProperty extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set property {{name}} in object {{object}}.";
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
