<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to clone an object. */
interface Cloneable
{
	//Public methods
	/**
	 * Clone into a new instance.
	 * 
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	public function clone(): object;
}
