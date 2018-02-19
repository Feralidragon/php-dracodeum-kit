<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Traits\LazyProperties\Exception;

/**
 * Lazy properties trait property creation failed exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever the creation of a property 
 * has failed.
 * 
 * @since 1.0.0
 */
class PropertyCreationFailed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property creation has failed in object {{object}}.\n" . 
			"HINT: A property may only be created from within a builder function.";
	}
}
