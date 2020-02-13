<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to get the scope UID (unique identifier) property names from an entity. */
trait ScopeUidPropertyNames
{
	//Protected methods
	/**
	 * Get scope UID (unique identifier) property names.
	 * 
	 * @return string[]
	 * <p>The scope UID (unique identifier) property names.</p>
	 */
	protected function getScopeUidPropertyNames(): array
	{
		return [];
	}
}
