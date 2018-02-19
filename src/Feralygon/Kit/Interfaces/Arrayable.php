<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/**
 * Arrayable interface.
 * 
 * This interface defines a method to convert an object into an array.
 * 
 * @since 1.0.0
 */
interface Arrayable
{
	//Public methods
	/**
	 * Convert this object into an array.
	 * 
	 * @since 1.0.0
	 * @return array <p>This object converted into an array.</p>
	 */
	public function toArray() : array;
}
