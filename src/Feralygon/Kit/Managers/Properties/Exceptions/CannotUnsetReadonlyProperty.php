<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * This exception is thrown from a properties manager whenever a given read-only property is attempted to be unset.
 * 
 * @since 1.0.0
 */
class CannotUnsetReadonlyProperty extends CannotUnsetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset read-only property {{property.getName()}} in properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
