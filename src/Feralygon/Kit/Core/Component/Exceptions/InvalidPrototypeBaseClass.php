<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Exceptions;

use Feralygon\Kit\Core\Component\Exception;

/**
 * Core component invalid prototype base class exception class.
 * 
 * This exception is thrown from a component whenever a given prototype base class is invalid.
 * 
 * @since 1.0.0
 * @property-read string $base_class <p>The base class.</p>
 */
class InvalidPrototypeBaseClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype base class {{base_class}} in component {{component}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('base_class', true);
	}
}
