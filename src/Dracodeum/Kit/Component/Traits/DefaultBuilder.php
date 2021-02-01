<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

trait DefaultBuilder
{
	//Protected static methods
	/**
	 * Get default builder function.
	 * 
	 * The returning function is used to build an instance during a coercion or evaluation if none is given.  
	 * It must be compatible with the following signature:  
	 * ```
	 * function ($prototype, array $properties): Dracodeum\Kit\Component
	 * ```
	 * 
	 * **Parameters:**
	 * - `Dracodeum\Kit\Prototype|string|null $prototype`  
	 *   The prototype instance, class or name to build with.  
	 *   If not set, then the default prototype instance or the base prototype class is used.  
	 *   &nbsp;
	 * - `array $properties`  
	 *   The properties to build with, as a set of `name => value` pairs.  
	 *   Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 *   in the same order as how these properties were first declared.  
	 *   &nbsp;
	 * 
	 * **Return:** `Dracodeum\Kit\Component`  
	 * The built instance.
	 * 
	 * @return callable|null
	 * The default builder function, or `null` if none is set.
	 */
	protected static function getDefaultBuilder(): ?callable
	{
		return null;
	}
}
