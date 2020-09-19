<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes\Input\Interfaces;

/** This interface defines a method to produce filters in an input prototype. */
interface FilterProducer
{
	//Public methods
	/**
	 * Produce filter for a given name with a given set of properties.
	 * 
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties
	 * <p>The properties to produce with, as a set of <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Dracodeum\Kit\Components\Input\Components\Modifiers\Filter|\Dracodeum\Kit\Components\Input\Prototypes\Modifiers\Filter|string|null
	 * <p>The produced filter component instance or name, or prototype instance, class or name, 
	 * for the given name with the given set of properties, or <code>null</code> if none was produced.</p>
	 */
	public function produceFilter(string $name, array $properties);
}
