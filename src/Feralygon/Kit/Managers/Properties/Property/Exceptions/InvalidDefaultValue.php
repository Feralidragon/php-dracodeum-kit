<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Property\Exceptions;

/**
 * This exception is thrown from a property whenever a given default value is invalid.
 * 
 * @since 1.0.0
 */
class InvalidDefaultValue extends InvalidValue
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid default value {{value}} for property {{property.getName()}} in properties manager " . 
			"with owner {{property.getManager().getOwner()}}.";
	}
}
