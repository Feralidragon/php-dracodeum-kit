<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to perform processing before an entity update. */
trait PreUpdateProcessor
{
	//Protected methods
	/**
	 * Perform processing before an update from a given set of old values to a new given set.
	 * 
	 * @param array $old_values
	 * <p>The old values to perform processing with, as a set of <samp>name => value</samp> pairs.</p>
	 * @param array $new_values [reference]
	 * <p>The new values to perform processing with, as a set of <samp>name => value</samp> pairs.</p>
	 */
	protected function processPreUpdate(array $old_values, array &$new_values): void {}
}
