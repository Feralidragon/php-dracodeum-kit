<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/** This interface defines a method to instantiate a class from an array. */
interface ArrayInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given array.
	 * 
	 * @param array $array
	 * <p>The array to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given array.</p>
	 */
	public static function fromArray(array $array): object;
}
