<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Components\Store\Structures\Uid;

/** This interface defines a method to check if a resource exists in a store prototype. */
interface Checker
{
	//Public methods
	/**
	 * Check if a resource identified with a given UID instance exists.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid $uid
	 * <p>The UID instance to identify with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the resource identified with the given UID instance exists.</p>
	 */
	public function exists(Uid $uid, bool $readonly): bool;
}
