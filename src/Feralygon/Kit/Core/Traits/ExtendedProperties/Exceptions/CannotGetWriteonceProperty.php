<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

/**
 * Core extended properties trait cannot get write-once property exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever a given write-once property 
 * with a given name is attempted to be retrieved.
 * 
 * @since 1.0.0
 */
class CannotGetWriteonceProperty extends CannotGetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get write-once property {{name}} from object {{object}}.";
	}
}
