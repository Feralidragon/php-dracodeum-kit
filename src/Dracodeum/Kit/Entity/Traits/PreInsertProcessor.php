<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity insert. */
trait PreInsertProcessor
{
	//Protected methods
	/**
	 * Perform processing before an insert with a given set of values.
	 * 
	 * @param array $values [reference]
	 * <p>The values to perform processing with, as a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected function processPreInsert(array &$values): void {}
}
