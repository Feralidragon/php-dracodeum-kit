<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/**
 * This trait defines a method to produce prototypes in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait PrototypeProducer
{
	//Protected methods
	/**
	 * Produce prototype for a given name.
	 * 
	 * @since 1.0.0
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Prototype|string|null
	 * <p>The produced prototype instance or class for the given name or <code>null</code> if none was produced.</p>
	 */
	protected function producePrototype(string $name, array $properties = [])
	{
		return null;
	}
}
