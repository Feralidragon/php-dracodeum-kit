<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enumerations;

use Dracodeum\Kit\Enumeration;

/**
 * This enumeration represents info levels, which are used to define which kind of information to return depending on 
 * the used level.
 */
class InfoLevel extends Enumeration
{
	//Public constants
	/** For the user interacting with the application. */
	public const ENDUSER = 0;
	
	/** For the developer integrating the application. */
	public const TECHNICAL = 1;
	
	/** For internal application messages and logging. */
	public const INTERNAL = 2;
}
