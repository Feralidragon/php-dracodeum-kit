<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to get the UID (unique identifier) property name from an entity. */
trait UidPropertyName
{
	//Protected methods
	/**
	 * Get UID (unique identifier) property name.
	 * 
	 * @return string|null
	 * <p>The UID (unique identifier) property name or <code>null</code> if none is set.</p>
	 */
	protected function getUidPropertyName(): ?string
	{
		return null;
	}
}
