<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents info scopes, which are used to define which kind of information to return 
 * depending on the used scope.
 */
class InfoScope extends Enumeration
{
	//Public constants
	/** Internal messages and logging. */
	public const INTERNAL = 0;
	
	/** Technical messages and information for external developers. */
	public const TECHNICAL = 1;
	
	/** Non-technical messages and information for end-users. */
	public const ENDUSER = 2;
}
