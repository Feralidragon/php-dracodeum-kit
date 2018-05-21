<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototype\Interfaces;

/**
 * This interface defines a method to get subcontracts from a prototype.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototype
 */
interface Subcontracts
{
	//Public static methods
	/**
	 * Get subcontract interface for a given name.
	 * 
	 * The returning subcontract interface should be implemented by the component using this prototype, 
	 * but such is not required.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to get for.</p>
	 * @return string|null
	 * <p>The subcontract interface for the given name or <code>null</code> if none is set.</p>
	 */
	public static function getSubcontract(string $name) : ?string;
}
