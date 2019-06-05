<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This trait turns a class into a non-instantiable one by preventing its instantiation.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Interfaces\NonInstantiable
 */
trait NonInstantiable
{
	//Final public magic methods
	/**
	 * Prevent class from being instantiated.
	 * 
	 * @since 1.0.0
	 */
	final public function __construct()
	{
		UCall::guard(false, ['error_message' => "This class cannot be instantiated."]);
	}
}
