<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to instantiate a class from a string. */
interface StringInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given string.
	 * 
	 * @param string $string
	 * <p>The string to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given string.</p>
	 */
	public static function fromString(string $string): object;
}
