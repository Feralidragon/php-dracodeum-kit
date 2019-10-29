<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	 * @param bool $recursive [default = false]
	 * <p>Check if it has been recursively set as read-only.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	public function isReadonly(bool $recursive = false): bool;
	
	/**
	 * Set as read-only.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Set all the possible referenced subobjects as read-only recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function setAsReadonly(bool $recursive = false): object;
}
