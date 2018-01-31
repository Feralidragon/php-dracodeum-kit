<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

/**
 * Core lazy properties trait cannot get write-only property exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given write-only property 
 * with a given name is attempted to be retrieved.
 * 
 * @since 1.0.0
 */
class CannotGetWriteonlyProperty extends CannotGetProperty
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot get write-only property {{name}} from object {{object}}.";
	}
}
