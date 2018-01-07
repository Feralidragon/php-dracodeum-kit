<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Exception;

/**
 * Core extended properties trait properties not initialized exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever properties have not been initialized yet.
 * 
 * @since 1.0.0
 */
class PropertiesNotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties have not been initialized yet in object {{object}}.\n" . 
			"HINT: Properties must be initialized first through the \"initializeProperties\" method.";
	}
}
