<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Enumerations\Halt;

use Dracodeum\Kit\Enumeration;

class Type extends Enumeration
{
	//Public constants
	/** The given resource was not found. */
	public const NOT_FOUND = 'NotFound';
	
	/** The given resource scope was not found. */
	public const SCOPE_NOT_FOUND = 'ScopeNotFound';
	
	/** The given resource is in conflict with another. */
	public const CONFLICT = 'Conflict';
}
