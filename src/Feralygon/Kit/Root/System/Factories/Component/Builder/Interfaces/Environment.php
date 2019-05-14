<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Factories\Component\Builder\Interfaces;

use Feralygon\Kit\Root\System\Components\Environment as Component;

/**
 * This interface defines a method to build an environment instance.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Root\System\Factories\Component
 */
interface Environment
{
	//Public methods
	/**
	 * Build instance with a given prototype and set of properties.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Root\System\Prototypes\Environment|string $prototype
	 * <p>The prototype instance, class or name to build with.</p>
	 * @param array $properties
	 * <p>The properties to build with, as <samp>name => value</samp> pairs.<br>
	 * Required properties may also be given as an array of values (<samp>[value1, value2, ...]</samp>), 
	 * in the same order as how these properties were first declared.</p>
	 * @return \Feralygon\Kit\Root\System\Components\Environment
	 * <p>The built instance with the given prototype and set of properties.</p>
	 */
	public function build($prototype, array $properties): Component;
}
