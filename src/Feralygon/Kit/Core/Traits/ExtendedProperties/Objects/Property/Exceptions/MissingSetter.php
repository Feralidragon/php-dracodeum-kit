<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exception;

/**
 * Core extended properties trait property object missing setter exception class.
 * 
 * This exception is thrown from an extended properties trait property object whenever a setter function is missing.
 * 
 * @since 1.0.0
 */
class MissingSetter extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Missing setter function in property {{property}}.";
	}
}
