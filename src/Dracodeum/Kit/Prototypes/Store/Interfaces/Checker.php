<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Structures\Uid;

/** This interface defines a method to check if a resource exists in a store prototype. */
interface Checker
{
	//Public methods
	/**
	 * Check if a resource with a given UID instance exists.
	 * 
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to check with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource with the given UID instance exists.</p>
	 */
	public function exists(Uid $uid, bool $readonly): bool;
}
