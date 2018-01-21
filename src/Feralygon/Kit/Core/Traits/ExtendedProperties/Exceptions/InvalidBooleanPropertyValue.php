<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

/**
 * Core extended properties trait invalid boolean property value exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever a given value is invalid for a given boolean property.
 * 
 * @since 1.0.0
 */
class InvalidBooleanPropertyValue extends InvalidPropertyValue
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid boolean value {{value}} for property {{name}} in object {{object}}.\n" . 
			"HINT: Only a boolean value is allowed.";
	}
}
