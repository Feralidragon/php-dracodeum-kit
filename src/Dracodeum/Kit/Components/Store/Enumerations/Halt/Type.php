<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Enumerations\Halt;

use Dracodeum\Kit\Enumeration;

class Type extends Enumeration
{
	//Public constants
	/** Used when a given resource was not found. */
	public const NOT_FOUND = 'NotFound';
	
	/** Used when a given resource scope was not found. */
	public const SCOPE_NOT_FOUND = 'ScopeNotFound';
	
	/** Used when a given resource is in conflict with another. */
	public const CONFLICT = 'Conflict';
}
