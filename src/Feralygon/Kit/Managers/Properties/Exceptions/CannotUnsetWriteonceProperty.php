<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * This exception is thrown from a properties manager whenever a given write-once property is attempted to be unset.
 * 
 * @since 1.0.0
 */
class CannotUnsetWriteonceProperty extends CannotUnsetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset write-once property {{property.getName()}} from properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
