<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store\Interfaces;

use Dracodeum\Kit\Structures\Uid;

/** This interface defines a method to insert a resource in a store prototype. */
interface Inserter
{
	//Public methods
	/**
	 * Insert a resource with a given UID instance with a given set of values.
	 * 
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to insert with.<br>
	 * It may be modified during insertion, such as when any of its properties are automatically generated.</p>
	 * @param array $values
	 * <p>The values to insert with, as <samp>name => value</samp> pairs.</p>
	 * @return array|null
	 * <p>The full or partial set of inserted values of the resource with the given UID instance, 
	 * as <samp>name => value</samp> pairs, or <code>null</code> if the resource already exists.</p>
	 */
	public function insert(Uid $uid, array $values): ?array;
}
