<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component;

use Dracodeum\Kit\Interfaces\Uncloneable as IUncloneable;
use Dracodeum\Kit\Traits;

/**
 * This class is the base to be extended from when creating a prototype producer.
 * 
 * A prototype producer is responsible for extending or overriding the prototype producer method of a component, 
 * removing the need of extending the component itself into a new class in order to override such a method directly.
 */
abstract class PrototypeProducer implements IUncloneable
{
	//Traits
	use Traits\EmptyConstructor;
	use Traits\Uncloneable;
	
	
	
	//Abstract public methods
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
	abstract public function produce(string $name, array $properties);
}
