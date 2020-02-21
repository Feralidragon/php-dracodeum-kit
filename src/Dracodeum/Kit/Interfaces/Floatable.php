<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to cast an object to a float. */
interface Floatable
{
	//Public methods
	/**
	 * Cast this object to a float.
	 * 
	 * @return float
	 * <p>This object cast to a float.</p>
	 */
	public function toFloat(): float;
}
