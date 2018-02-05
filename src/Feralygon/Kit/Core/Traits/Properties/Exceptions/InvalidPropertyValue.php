<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;

/**
 * Core properties trait invalid property value exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever a given value 
 * is invalid for a given property.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 * @property-read mixed $value <p>The property value.</p>
 */
class InvalidPropertyValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{name}} in object {{object}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
		$this->addMixedProperty('value', true);
	}
}
