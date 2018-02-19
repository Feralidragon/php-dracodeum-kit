<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Objects\Property\Exceptions;

use Feralygon\Kit\Managers\Properties\Objects\Property\Exception;

/**
 * Properties manager property object already initialized exception class.
 * 
 * This exception is thrown from a property object whenever it has already been initialized.
 * 
 * @since 1.0.0
 */
class AlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property {{property.getName()}} from properties manager " . 
			"with owner {{property.getManager().getOwner()}} has already been initialized.";
	}
}
