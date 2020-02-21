<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to instantiate a class from an integer. */
interface IntegerInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given integer.
	 * 
	 * @param int $integer
	 * <p>The integer to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given integer.</p>
	 */
	public static function fromInteger(int $integer): object;
}
