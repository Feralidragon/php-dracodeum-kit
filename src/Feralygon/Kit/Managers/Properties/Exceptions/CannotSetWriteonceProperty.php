<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * This exception is thrown from a properties manager whenever a given write-once property is attempted to be set.
 * 
 * @since 1.0.0
 */
class CannotSetWriteonceProperty extends CannotSetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set write-once property {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
