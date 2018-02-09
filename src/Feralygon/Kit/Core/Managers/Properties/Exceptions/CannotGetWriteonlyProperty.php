<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

/**
 * Core properties manager cannot get write-only property exception class.
 * 
 * This exception is thrown from a properties manager whenever a given write-only property is attempted to be retrieved.
 * 
 * @since 1.0.0
 */
class CannotGetWriteonlyProperty extends CannotGetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get write-only property {{property.getName()}} from properties manager " . 
			"with owner {{manager.getOwner()}}.";
	}
}
