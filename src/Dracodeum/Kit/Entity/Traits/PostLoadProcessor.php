<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing after an entity load. */
trait PostLoadProcessor
{
	//Protected methods
	/**
	 * Perform processing after a load.
	 * 
	 * @return void
	 */
	protected function processPostLoad(): void {}
}
