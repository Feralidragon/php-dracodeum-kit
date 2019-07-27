<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This trait turns a class into an uncloneable one by preventing the cloning of its instances.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Interfaces\Uncloneable
 */
trait Uncloneable
{
	//Final public magic methods
	/**
	 * Prevent class instance from being cloned.
	 * 
	 * @since 1.0.0
	 */
	final public function __clone()
	{
		UCall::guard(false, ['error_message' => "Instances of this class cannot be cloned."]);
	}
}
