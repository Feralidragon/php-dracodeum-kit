<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing after an entity delete. */
trait PostDeleteProcessor
{
	//Protected methods
	/**
	 * Perform processing after a delete.
	 * 
	 * @return void
	 */
	protected function processPostDelete(): void {}
}
