<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Vector\Exceptions;

use Feralygon\Kit\Primitives\Vector\Exception;

/**
 * This exception is thrown from a vector whenever no values are set.
 * 
 * @since 1.0.0
 */
class ValuesNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No values set in vector {{vector}}.";
	}
}
