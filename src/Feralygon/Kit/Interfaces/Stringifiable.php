<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/**
 * Stringifiable interface.
 * 
 * This interface defines a method to convert an object into a string.
 * 
 * @since 1.0.0
 */
interface Stringifiable
{
	//Public methods
	/**
	 * Convert this object into a string.
	 * 
	 * @since 1.0.0
	 * @return string <p>This object converted into a string.</p>
	 */
	public function toString() : string;
}
