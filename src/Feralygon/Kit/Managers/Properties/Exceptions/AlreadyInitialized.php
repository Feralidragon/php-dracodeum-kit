<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Exceptions;

use Feralygon\Kit\Managers\Properties\Exception;

/**
 * This exception is thrown from a properties manager whenever it has already been initialized.
 * 
 * @since 1.0.0
 */
class AlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties manager with owner {{manager.getOwner()}} has already been initialized.";
	}
}
