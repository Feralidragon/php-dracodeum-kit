<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exception;

/**
 * Core properties manager property object no default value set exception class.
 * 
 * This exception is thrown from a property object whenever no default value has been set.
 * 
 * @since 1.0.0
 */
class NoDefaultValueSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "No default value has been set in property {{property}}.";
	}
}
