<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	 * @param bool $recursive [default = false]
	 * <p>Unpersist all the possible referenced subobjects recursively (if applicable).</p>
	 * @return $this
	 * <p>This instance, for chaining purposes.</p>
	 */
	public function unpersist(bool $recursive = false): object;
}
