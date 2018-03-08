<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factories\Component\Builder\Interfaces;

use Feralygon\Kit\Components\Input as Component;

/**
 * Component factory input builder interface.
 * 
 * This interface defines a method to build an input component instance.
 * 
 * @since 1.0.0
 */
interface Input
{
	//Public methods
	/**
	 * Build instance with a given prototype.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototypes\Input|string $prototype <p>The prototype instance, class or 
	 * name to build with.</p>
	 * @param array $properties [default = []] <p>The properties to build with, as <samp>name => value</samp> pairs.</p>
	 * @param array $prototype_properties [default = []] <p>The prototype properties to build with, 
	 * as <samp>name => value</samp> pairs.</p>
	 * @return \Feralygon\Kit\Components\Input <p>The built instance with the given prototype.</p>
	 */
	public function build($prototype, array $properties = [], array $prototype_properties = []) : Component;
}