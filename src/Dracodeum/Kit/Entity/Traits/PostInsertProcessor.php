<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing after an entity instance is inserted. */
trait PostInsertProcessor
{
	//Protected methods
	/**
	 * Perform processing after an instance is inserted with a given set of values.
	 * 
	 * @param array $values
	 * <p>The values to perform processing with, as <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected function processPostInsert(array $values): void {}
}
