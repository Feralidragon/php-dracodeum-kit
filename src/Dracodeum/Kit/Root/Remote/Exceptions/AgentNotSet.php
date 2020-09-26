<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Remote\Exceptions;

use Dracodeum\Kit\Root\Remote\Exception;

/** This exception is thrown from the remote class whenever an agent is not set. */
class AgentNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No agent set.";
	}
}
