<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Interfaces;

/** This interface defines a method to produce constraints in an input prototype. */
interface ConstraintProducer
{
	//Public methods
	/**
	 * Produce constraint for a given name with a given set of properties.
	 * 
	 * @param string $name
	 * <p>The name to produce for.</p>
	 * @param array $properties
	 * <p>The properties to produce with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint|\Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint|string|null
	 * <p>The produced constraint component instance or name, or prototype instance, class or name, 
	 * for the given name with the given set of properties, or <code>null</code> if none was produced.</p>
	 */
	public function produceConstraint(string $name, array $properties);
}
