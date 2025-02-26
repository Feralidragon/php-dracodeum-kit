<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Structures\Uid;

/** This interface defines a method to update a resource in a store prototype. */
interface Updater
{
	//Public methods
	/**
	 * Update a resource with a given UID instance with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to update with.</p>
	 * @param array $values
	 * <p>The values to update with, as a set of <samp>name => value</samp> pairs.</p>
	 * @return array|null
	 * <p>The full or partial set of updated values of the resource with the given UID instance, 
	 * as a set of <samp>name => value</samp> pairs, or <code>null</code> if the resource was not found.</p>
	 */
	public function update(Uid $uid, array $values): ?array;
}
