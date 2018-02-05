<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;

/**
 * Core functions trait function already bound exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever a given function has already been bound.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The function name.</p>
 */
class FunctionAlreadyBound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Function {{name}} has already been bound in object {{object}}.";
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
