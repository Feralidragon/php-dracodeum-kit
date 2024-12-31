<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

trait PrototypeProducer
{
	//Protected methods
	/**
	 * Produce prototype.
	 * 
	 * @param string $name
	 * The name to produce for.
	 * 
	 * @param array $properties
	 * The properties to produce with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @return \Dracodeum\Kit\Prototype|string|null
	 * The produced prototype instance or class, or `null` if none was produced.
	 */
	protected function producePrototype(string $name, array $properties)
	{
		return null;
	}
}
