<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

/**
 * Core extended properties trait cannot unset read-only property exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever a given read-only property with a given name is attempted to be unset.
 * 
 * @since 1.0.0
 */
class CannotUnsetReadonlyProperty extends CannotUnsetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot unset read-only property {{name}} from object {{object}}.";
	}
}
