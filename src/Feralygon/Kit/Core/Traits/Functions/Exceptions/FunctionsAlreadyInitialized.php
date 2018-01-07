<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;

/**
 * Core functions trait functions already initialized exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever functions have already been initialized.
 * 
 * @since 1.0.0
 */
class FunctionsAlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Functions have already been initialized in object {{object}}.";
	}
}
