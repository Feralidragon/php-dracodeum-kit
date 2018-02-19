<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * Properties manager cannot set read-only property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given read-only property is attempted to be set.
 * 
 * @since 1.0.0
 */
class CannotSetReadonlyProperty extends CannotSetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set read-only property {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
