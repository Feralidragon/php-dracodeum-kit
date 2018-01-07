<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input\Exception;

/**
 * Core input component filter properties not allowed exception class.
 * 
 * This exception is thrown from an input whenever filter properties are given although they are not allowed.
 * 
 * @since 1.0.0
 */
class FilterPropertiesNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Filter properties not allowed in input {{component}} (with prototype {{prototype}}).\n" . 
			"HINT: Filter properties are only allowed whenever the filter is given as either a class or a name.";
	}
}
