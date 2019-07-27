<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

/** 
 * This trait defines a method to produce the default prototype in a component.
 * 
 * @see \Feralygon\Kit\Component
 */
trait DefaultPrototypeProducer
{
	//Protected methods
	/**
	 * Produce default prototype with a given set of properties.
	 * 
	 * The returning prototype is used if none is given during instantiation.<br>
	 * If none is produced, then the base prototype class is used instead.
	 * 
	 * @param array $properties
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Prototype|string|null
	 * <p>The produced default prototype instance or class with the given set of properties 
	 * or <code>null</code> if none was produced.</p>
	 */
	protected function produceDefaultPrototype(array $properties)
	{
		return null;
	}
}
