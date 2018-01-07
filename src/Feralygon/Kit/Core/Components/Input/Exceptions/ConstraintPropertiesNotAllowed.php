<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input\Exception;

/**
 * Core input component constraint properties not allowed exception class.
 * 
 * This exception is thrown from an input whenever constraint properties are given although they are not allowed.
 * 
 * @since 1.0.0
 */
class ConstraintPropertiesNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Constraint properties not allowed in input {{component}} (with prototype {{prototype}}).\n" . 
			"HINT: Constraint properties are only allowed whenever the constraint is given as either a class or a name.";
	}
}
