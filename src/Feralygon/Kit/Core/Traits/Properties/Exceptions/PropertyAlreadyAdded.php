<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;

/**
 * Core properties trait property already added exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever a given property has already been added.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 */
class PropertyAlreadyAdded extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property {{name}} has already been added to object {{object}}.";
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
