<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Structures\Uid;

/** This interface defines a method to select a resource from a store prototype. */
interface Selecter
{
	//Public methods
	/**
	 * Select a resource with a given UID instance.
	 * 
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to select with.</p>
	 * @param bool $readonly
	 * <p>Perform the query as a read-only operation.</p>
	 * @return array|null
	 * <p>The selected resource with the given UID instance, as a set of <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none is set.</p>
	 */
	public function select(Uid $uid, bool $readonly): ?array;
}
