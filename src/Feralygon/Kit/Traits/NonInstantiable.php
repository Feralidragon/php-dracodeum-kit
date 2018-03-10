<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Traits\NonInstantiable\Exceptions;

/**
 * This trait turns a class into a non-instantiable one by preventing its instantiation.
 * 
 * @since 1.0.0
 */
trait NonInstantiable
{
	//Final public magic methods
	/**
	 * Prevent class from being instantiated.
	 * 
	 * @since 1.0.0
	 * @throws \Feralygon\Kit\Traits\NonInstantiable\Exceptions\CannotInstantiate
	 */
	final public function __construct()
	{
		throw new Exceptions\CannotInstantiate(['class' => static::class]);
	}
}
