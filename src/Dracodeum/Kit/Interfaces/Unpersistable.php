<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to enable the unpersistence of an object. */
interface Unpersistable
{
	//Public methods
	/**
	 * Unpersist.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function unpersist(): object;
}
