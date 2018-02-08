<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;

/**
 * Core properties manager no builder set exception class.
 * 
 * This exception is thrown from a properties manager whenever no builder function has been set.
 * 
 * @since 1.0.0
 */
class NoBuilderSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "No builder function has been set in properties manager with owner {{manager.getOwner()}}.\n" . 
			"HINT: A builder function is required to be set when lazy-loading is enabled.";
	}
}
