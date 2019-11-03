<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Exceptions;

use Dracodeum\Kit\Component\Exception;

/** This exception is thrown from a component whenever no proxy is set. */
class ProxyNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No proxy set in component {{component}}.";
	}
}