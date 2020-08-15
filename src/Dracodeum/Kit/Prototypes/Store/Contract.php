<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Store;

use Dracodeum\Kit\Structures\Uid;

/** This interface defines a contract as a method to be implemented by any component set to use a store prototype. */
interface Contract
{
	//Public methods
	/**
	 * Halt the current function or method call in the stack with a given UID instance and type.
	 * 
	 * @see \Dracodeum\Kit\Components\Store\Enumerations\Halt\Type
	 * @param \Dracodeum\Kit\Structures\Uid $uid
	 * <p>The UID instance to halt with.</p>
	 * @param string $type
	 * <p>The type to halt with, 
	 * as a name or value from the <code>Dracodeum\Kit\Components\Store\Enumerations\Halt\Type</code> enumeration.</p>
	 * @return void
	 */
	public function halt(Uid $uid, string $type): void;
}
