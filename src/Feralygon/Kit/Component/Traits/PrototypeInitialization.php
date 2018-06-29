<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component\Traits;

use Feralygon\Kit\Prototype;

/**
 * This trait defines a method to initialize a prototype instance in a component.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Component
 */
trait PrototypeInitialization
{
	//Protected methods
	/**
	 * Initialize prototype instance.
	 * 
	 * @since 1.0.0
	 * @param \Feralygon\Kit\Prototype $prototype
	 * <p>The prototype instance to initialize.</p>
	 * @return void
	 */
	protected function initializePrototype(Prototype $prototype): void {}
}
