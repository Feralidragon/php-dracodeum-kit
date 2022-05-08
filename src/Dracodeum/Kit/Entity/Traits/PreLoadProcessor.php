<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity load. */
trait PreLoadProcessor
{
	//Protected static methods
	/**
	 * Perform processing before the load of a given set of values.
	 * 
	 * The given set of values may be set to <code>null</code> in order to halt the load of this entity.
	 * 
	 * @param array $values [reference]
	 * <p>The values to perform processing with, as a set of <samp>name => value</samp> pairs.</p>
	 */
	protected static function processPreLoad(array &$values): void {}
}
