<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Exceptions;

/**
 * Core extended lazy properties trait cannot set write-once property exception class.
 * 
 * This exception is thrown from an object using the extended lazy properties trait whenever 
 * a given write-once property with a given name is attempted to be set.
 * 
 * @since 1.0.0
 */
class CannotSetWriteonceProperty extends CannotSetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set write-once property {{name}} in object {{object}}.";
	}
}
