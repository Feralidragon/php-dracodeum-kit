<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	use Traits\NoConstructor;
	use Traits\Uncloneable;
	
	
	
	//Abstract public methods
	/**
	 * Produce prototype for a given name with a given set of properties.
	 * 
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties
	 * <p>The properties to produce with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Prototype|string|null
	 * <p>The produced prototype instance or class for the given name with the given set of properties 
	 * or <code>null</code> if none was produced.</p>
	 */
	abstract public function produce(string $name, array $properties);
}
