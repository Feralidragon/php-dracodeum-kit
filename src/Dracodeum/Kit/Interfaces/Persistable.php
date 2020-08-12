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
	 * @param bool $recursive [default = false]
	 * <p>Check if has already been recursively persisted at least once.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if has already been persisted at least once.</p>
	 */
	public function isPersisted(bool $recursive = false): bool;
	
	/**
	 * Persist.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Persist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function persist(bool $recursive = false): object;
}
