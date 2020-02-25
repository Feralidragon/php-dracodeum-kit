<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Components\Store\Structures\Uid;

/** This interface defines a method to return a resource in a store prototype. */
interface Returner
{
	//Public methods
	/**
	 * Return a resource with a given UID instance.
	 * 
	 * @param \Dracodeum\Kit\Components\Store\Structures\Uid $uid
	 * <p>The UID instance to return with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return array|null
	 * <p>The resource with the given UID instance, as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none is set.</p>
	 */
	public function return(Uid $uid, bool $readonly): ?array;
}
