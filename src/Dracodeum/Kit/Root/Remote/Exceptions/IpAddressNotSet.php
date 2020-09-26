<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Remote\Exceptions;

use Dracodeum\Kit\Root\Remote\Exception;

/** This exception is thrown from the remote class whenever an IP address is not set. */
class IpAddressNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No IP address set.";
	}
}
