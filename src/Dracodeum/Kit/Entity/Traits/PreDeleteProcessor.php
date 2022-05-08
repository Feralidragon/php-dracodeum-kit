<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity delete. */
trait PreDeleteProcessor
{
	//Protected methods
	/** Perform processing before a delete. */
	protected function processPreDelete(): void {}
}
