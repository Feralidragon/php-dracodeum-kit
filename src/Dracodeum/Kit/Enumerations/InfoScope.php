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
	/** Internal info scope, for internal application messages and logging. */
	public const INTERNAL = 0;
	
	/** Technical info scope, for the developer using the application. */
	public const TECHNICAL = 1;
	
	/** End-user info scope, for the user interacting with the application. */
	public const ENDUSER = 2;
}
