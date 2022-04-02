<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to instantiate a class from a callable. */
interface CallableInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given callable.
	 * 
	 * @param callable $callable
	 * <p>The callable to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given callable.</p>
	 */
	public static function fromCallable(callable $callable): object;
}
