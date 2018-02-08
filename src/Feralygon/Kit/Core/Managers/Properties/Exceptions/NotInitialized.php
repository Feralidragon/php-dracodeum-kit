<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;

/**
 * Core properties manager not initialized exception class.
 * 
 * This exception is thrown from a properties manager whenever it has not been initialized yet.
 * 
 * @since 1.0.0
 */
class NotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties manager with owner {{manager.getOwner()}} has not been initialized yet.\n" . 
			"HINT: The properties manager must be initialized first through the \"initialize\" method.";
	}
}
