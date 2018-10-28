<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Factories\Component\Builder\Interfaces;

use Feralygon\Kit\Components\Input\Components\Modifiers\Constraint as Component;

/**
 * This interface defines a method to build a constraint instance.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Factories\Component
 */
interface Constraint
{
	//Public methods
	/**
	 * Build instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Components\Input\Prototypes\Modifiers\Constraint|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties [default = []]
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Components\Input\Components\Modifiers\Constraint
	 * <p>The built instance with the given prototype.</p>
	 */
	public function build($prototype, array $properties = []): Component;
}
