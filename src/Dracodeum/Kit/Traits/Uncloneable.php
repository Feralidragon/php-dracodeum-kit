<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait turns a class into an uncloneable one by preventing the cloning of its instances.
 * 
 * @see \Dracodeum\Kit\Interfaces\Uncloneable
 */
trait Uncloneable
{
	//Final public magic methods
	/** Prevent class instance from being cloned. */
	final public function __clone(): void
	{
		UCall::halt(['error_message' => "Instances of this class cannot be cloned."]);
	}
}
