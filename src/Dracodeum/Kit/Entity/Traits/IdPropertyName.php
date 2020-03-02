<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to get the ID property name from an entity. */
trait IdPropertyName
{
	//Protected static methods
	/**
	 * Get ID property name.
	 * 
	 * @return string|null
	 * <p>The ID property name or <code>null</code> if none is set.</p>
	 */
	protected static function getIdPropertyName(): ?string
	{
		return null;
	}
}
