<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input\Exception;

/**
 * Core input component modifier properties not allowed exception class.
 * 
 * This exception is thrown from an input whenever modifier properties are given although they are not allowed.
 * 
 * @since 1.0.0
 */
class ModifierPropertiesNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Modifier properties not allowed in input {{component}} (with prototype {{prototype}}).\n" . 
			"HINT: Modifier properties are only allowed whenever the modifier is given as a name.";
	}
}
