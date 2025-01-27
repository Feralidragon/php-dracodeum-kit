<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\ComponentV2;

final class BlueEntry
{
	//Final public magic methods
	/**
	 * Instantiate class.
	 * 
	 * @param string $class
	 * The class to instantiate with.
	 * 
	 * @param array $properties
	 * The properties to instantiate with.
	 */
	final public function __construct(
		public readonly string $class,
		public readonly array $properties = []
	) {}
}
