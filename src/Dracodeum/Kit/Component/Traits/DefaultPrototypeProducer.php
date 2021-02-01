<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

trait DefaultPrototypeProducer
{
	//Protected methods
	/**
	 * Produce default prototype.
	 * 
	 * The returning prototype is used if none is given during instantiation.  
	 * If none is produced, then the base prototype class is used instead.
	 * 
	 * @param array $properties
	 * The properties to produce with, as a set of `name => value` pairs.  
	 * Required properties may also be given as an array of values (`[value1, value2, ...]`), 
	 * in the same order as how these properties were first declared.
	 * 
	 * @return \Dracodeum\Kit\Prototype|string|null
	 * The produced default prototype instance or class, or `null` if none was produced.
	 */
	protected function produceDefaultPrototype(array $properties)
	{
		return null;
	}
}
