<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Enums\Info;

enum Level: int
{
	/** Non-technical messages, data and overall information for end-users. */
	case ENDUSER = 0;
	
	/** Technical messages, data and overall information for external developers. */
	case TECHNICAL = 1;
	
	/** Internal messages, data, logging and overall information for internal maintainers. */
	case INTERNAL = 2;
}
