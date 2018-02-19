<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

/**
 * Properties manager cannot get write-once property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given write-once property is attempted to be retrieved.
 * 
 * @since 1.0.0
 */
class CannotGetWriteonceProperty extends CannotGetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get write-once property {{property.getName()}} from properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
