<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Type\Exceptions;

use Feralygon\Kit\Utilities\Type\Exception;

/**
 * This exception is thrown from the type utility whenever a given object or class is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $object_class
 * <p>The object or class.</p>
 */
class InvalidObjectClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid object or class {{object_class}}.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('object_class')->setAsRequired();
	}
}
