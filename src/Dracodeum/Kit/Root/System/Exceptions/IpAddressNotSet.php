<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Exceptions;

use Dracodeum\Kit\Root\System\Exception;

/** This exception is thrown from the system whenever no IP address is set. */
class IpAddressNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No IP address set.";
	}
}
