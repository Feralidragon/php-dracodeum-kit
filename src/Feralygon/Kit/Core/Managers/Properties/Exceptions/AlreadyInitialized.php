<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;

/**
 * Core properties manager already initialized exception class.
 * 
 * This exception is thrown from a properties manager whenever it has already been initialized.
 * 
 * @since 1.0.0
 */
class AlreadyInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties manager {{manager}} has already been initialized.";
	}
}
