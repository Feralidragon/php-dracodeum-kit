<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a set of methods to enable the persistence of an object. */
interface Persistable
{
	//Public methods
	/**
	 * Check if has already been persisted at least once.
	 * 
	 * @return bool
	 * <p>Boolean <code>true</code> if has already been persisted at least once.</p>
	 */
	public function isPersisted(): bool;
	
	/**
	 * Persist.
	 * 
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function persist(): object;
}
