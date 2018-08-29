<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Interfaces;

/**
 * This interface defines a method to instantiate a class from a string.
 * 
 * @since 1.0.0
 */
interface StringInstantiable
{
	//Public static methods
	/**
	 * Instantiate class from a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to instantiate from.</p>
	 * @return static
	 * <p>The class instance from the given string.</p>
	 */
	public static function fromString(string $string): object;
}
