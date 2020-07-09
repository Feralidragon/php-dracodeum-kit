<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

use Dracodeum\Kit\Structures\Uid as UidStructure;

/** This interface defines a method to get the UID instance from an object. */
interface Uid
{
	//Public methods
	/**
	 * Get UID instance.
	 * 
	 * @return \Dracodeum\Kit\Structures\Uid
	 * <p>The UID instance.</p>
	 */
	public function getUid(): UidStructure;
}
