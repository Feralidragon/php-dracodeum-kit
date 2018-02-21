<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Readonly\Exceptions;

use Feralygon\Kit\Traits\Readonly\Exception;

/**
 * Read-only trait read-only already initialized exception class.
 * 
 * This exception is thrown from an object using the read-only trait whenever read-only support 
 * has already been initialized.
 * 
 * @since 1.0.0
 */
class ReadonlyAlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Read-only support has already been initialized in object {{object}}.";
	}
}
