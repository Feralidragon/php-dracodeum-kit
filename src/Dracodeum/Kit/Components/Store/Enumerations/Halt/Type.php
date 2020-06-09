<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store\Enumerations\Halt;

use Dracodeum\Kit\Enumeration;

/** This enumeration represents halt types. */
class Type extends Enumeration
{
	//Public constants
	/** Not found halt type, used when a given resource was not found. */
	public const NOT_FOUND = 'NotFound';
	
	/** Scope not found halt type, used when a given resource scope was not found. */
	public const SCOPE_NOT_FOUND = 'ScopeNotFound';
	
	/** Conflict halt type, used when a given resource is in conflict with another. */
	public const CONFLICT = 'Conflict';
}
