<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

/** 
 * This interface defines a method to get a string for a given UID scope placeholder from a given value from a store 
 * prototype.
 */
interface UidScopePlaceholderValueString
{
	//Public methods
	/**
	 * Get string for a given UID scope placeholder from a given value.
	 * 
	 * @param string $placeholder
	 * <p>The placeholder to get for.</p>
	 * @param mixed $value
	 * <p>The value to get from.</p>
	 * @return string|null
	 * <p>The string for the given UID scope placeholder from the given value or <code>null</code> if none is set.</p>
	 */
	public function getUidScopePlaceholderValueString(string $placeholder, $value): ?string;
}
