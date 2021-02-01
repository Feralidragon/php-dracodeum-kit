<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component\Traits;

use Dracodeum\Kit\Prototype;

trait PrototypeInitializer
{
	//Protected methods
	/**
	 * Initialize prototype.
	 * 
	 * @param \Dracodeum\Kit\Prototype $prototype
	 * The prototype instance to initialize.
	 * 
	 * @return void
	 */
	protected function initializePrototype(Prototype $prototype): void {}
}
