<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to instantiate a class from a float. */
interface FloatInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given float.
	 * 
	 * @param float $float
	 * <p>The float to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given float.</p>
	 */
	public static function fromFloat(float $float): object;
}
