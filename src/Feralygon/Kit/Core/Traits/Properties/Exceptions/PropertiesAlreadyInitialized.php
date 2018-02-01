<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;

/**
 * Core properties trait properties already initialized exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever properties have already been initialized.
 * 
 * @since 1.0.0
 */
class PropertiesAlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties have already been initialized in object {{object}}.";
	}
}
