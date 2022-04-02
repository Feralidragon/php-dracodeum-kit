<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

trait PreInitializer
{
	//Protected methods
	/**
	 * Pre-initialize.
	 * 
	 * @param \Dracodeum\Kit\Prototype|string|null $prototype
	 * The prototype instance, class or name to pre-initialize with.
	 * 
	 * @param array $properties
	 * The properties to pre-initialize with, as a set of `name => value` pairs.
	 * 
	 * @return void
	 */
	protected function preInitialize(&$prototype, array &$properties): void {}
}
