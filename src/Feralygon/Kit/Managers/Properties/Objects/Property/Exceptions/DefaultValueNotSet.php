<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions;

use Feralygon\Kit\Managers\Properties\Objects\Property\Exception;

/**
 * This exception is thrown from a property object whenever no default value is set.
 * 
 * @since 1.0.0
 */
class DefaultValueNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "No default value set in property {{property.getName()}} in properties manager " . 
			"with owner {{property.getManager().getOwner()}}.";
	}
}
