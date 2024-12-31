<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

/** 
 * This interface defines a method to get a string for a given UID scope placeholder from a given ID from a store 
 * prototype.
 */
interface UidScopePlaceholderIdString
{
	//Public methods
	/**
	 * Get string for a given UID scope placeholder from a given ID.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to get for.</p>
	 * @param int|string $id
	 * <p>The ID to get from.</p>
	 * @return string|null
	 * <p>The string for the given UID scope placeholder from the given ID or <code>null</code> if none is set.</p>
	 */
	public function getUidScopePlaceholderIdString(string $placeholder, $id): ?string;
}
