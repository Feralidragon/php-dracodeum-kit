<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

/**
 * Core properties manager cannot set read-only property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given read-only property with a given name 
 * is attempted to be set.
 * 
 * @since 1.0.0
 */
class CannotSetReadonlyProperty extends CannotSetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set read-only property {{name}} in properties manager with owner {{manager.getOwner()}}.";
	}
}
