<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Data\Exceptions;

use Feralygon\Kit\Utilities\Data\Exception;

/** This exception is thrown from the data utility whenever an empty array is given. */
class EmptyArray extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Empty array given.";
	}
}
