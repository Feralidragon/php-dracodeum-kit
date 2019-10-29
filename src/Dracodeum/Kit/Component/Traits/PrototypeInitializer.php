<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

use Dracodeum\Kit\Prototype;

/** This trait defines a method to initialize a prototype instance in a component. */
trait PrototypeInitializer
{
	//Protected methods
	/**
	 * Initialize prototype instance.
	 * 
	 * @param \Dracodeum\Kit\Prototype $prototype
	 * <p>The prototype instance to initialize.</p>
	 * @return void
	 */
	protected function initializePrototype(Prototype $prototype): void {}
}
