<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Component\Builder\Interfaces;

use Feralygon\Kit\Components\Input as Component;

/** This interface defines a method to build an input instance. */
interface Input
{
	//Public methods
	/**
	 * Build instance with a given prototype and set of properties.
	 * 
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties
	 * <p>The properties to build with, as <samp>name => value</samp> pairs, if a prototype class or name is given.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Components\Input
	 * <p>The built instance with the given prototype and set of properties.</p>
	 */
	public function build($prototype, array $properties): Component;
}
