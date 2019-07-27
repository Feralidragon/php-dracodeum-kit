<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/**
 * This trait defines a method to produce prototypes in a component.
 * 
 * @see \Feralygon\Kit\Component
 */
trait PrototypeProducer
{
	//Protected methods
	/**
	 * Produce prototype for a given name with a given set of properties.
	 * 
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Prototype|string|null
	 * <p>The produced prototype instance or class for the given name with the given set of properties 
	 * or <code>null</code> if none was produced.</p>
	 */
	protected function producePrototype(string $name, array $properties)
	{
		return null;
	}
}
