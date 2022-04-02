<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to cast an object to an integer. */
interface Integerable
{
	//Public methods
	/**
	 * Cast this object to an integer.
	 * 
	 * @return int
	 * <p>This object cast to an integer.</p>
	 */
	public function toInteger(): int;
}
