<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/**
 * This interface defines a set of methods to check and set an object as read-only.
 * 
 * @since 1.0.0
 */
interface Readonlyable
{
	//Public methods
	/**
	 * Check if is read-only.
	 * 
	 * @since 1.0.0
	 * @return bool
	 * <p>Boolean <code>true</code> if is read-only.</p>
	 */
	public function isReadonly(): bool;
	
	/**
	 * Set as read-only.
	 * 
	 * @since 1.0.0
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function setAsReadonly(): object;
}
