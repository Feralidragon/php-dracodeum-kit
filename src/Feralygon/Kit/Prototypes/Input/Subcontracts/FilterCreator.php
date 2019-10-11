<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Subcontracts;

use Feralygon\Kit\Components\Input\Components\Modifiers\Filter;

/**
 * This interface defines a subcontract as a method to create filter instances, 
 * which may be implemented by any component set to use an input prototype.
 */
interface FilterCreator
{
	//Public methods
	/**
	 * Create a filter instance with a given prototype.
	 * 
	 * @param \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter|string $prototype
	 * <p>The prototype instance, class or name to create with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to create with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Filter
	 * <p>The created filter instance with the given prototype.</p>
	 */
	public function createFilter($prototype, array $properties = []): Filter;
}
