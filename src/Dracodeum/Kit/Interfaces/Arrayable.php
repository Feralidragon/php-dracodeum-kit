<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to cast an object to an array. */
interface Arrayable
{
	//Public methods
	/**
	 * Cast this object to an array.
	 * 
	 * @return array
	 * <p>An array cast from this object.</p>
	 */
	public function toArray(): array;
}
