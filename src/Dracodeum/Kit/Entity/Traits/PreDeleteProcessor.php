<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity delete. */
trait PreDeleteProcessor
{
	//Protected methods
	/**
	 * Perform processing before a delete.
	 * 
	 * @return void
	 */
	protected function processPreDelete(): void {}
}
