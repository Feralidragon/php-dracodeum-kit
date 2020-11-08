<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Properties\Property\Exceptions;

use Dracodeum\Kit\Managers\Properties\Property\Exception;

class DefaultValueNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No default value set in property {{property.getName()}} in manager " . 
			"with owner {{property.getManager().getOwner()}}.";
	}
}
