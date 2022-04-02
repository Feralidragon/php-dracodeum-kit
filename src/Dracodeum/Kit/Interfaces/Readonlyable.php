<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a set of methods to check and set an object as read-only. */
interface Readonlyable
{
	//Public methods
	/**
	 * Check if is read-only.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	public function isReadonly(): bool;
	
	/**
	 * Set as read-only.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function setAsReadonly(): object;
}
