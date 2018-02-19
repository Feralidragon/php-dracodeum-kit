<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Exceptions;

use Feralygon\Kit\Component\Exception;

/**
 * Component prototype properties not allowed exception class.
 * 
 * This exception is thrown from a component whenever prototype properties are given although they are not allowed.
 * 
 * @since 1.0.0
 */
class PrototypePropertiesNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Prototype properties not allowed in component {{component}}.\n" . 
			"HINT: Prototype properties are only allowed whenever the prototype is given as a class " . 
			"or not given at all.";
	}
}
