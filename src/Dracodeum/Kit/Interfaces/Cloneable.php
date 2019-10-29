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
	 * @param bool $recursive [default = false]
	 * <p>Clone all the possible referenced subobjects into new instances recursively (if applicable).</p>
	 * @return static
	 * <p>The new cloned instance from this one.</p>
	 */
	public function clone(bool $recursive = false): object;
}
