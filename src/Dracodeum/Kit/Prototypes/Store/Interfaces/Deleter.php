<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Components\Store\Structures\Uid;

/** This interface defines a method to delete a resource in a store prototype. */
interface Deleter
{
	//Public methods
	/**
	 * Delete a resource with a given UID instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid $uid
	 * <p>The UID instance to delete with.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given UID instance was found and deleted.</p>
	 */
	public function delete(Uid $uid): bool;
}
