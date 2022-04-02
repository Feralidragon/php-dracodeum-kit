<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity\Traits;

/** This trait defines a method to process a set of log event data properties in an entity. */
trait LogEventDataPropertiesProcessor
{
	//Protected methods
	/**
	 * Process a given set of log event data properties.
	 * 
	 * @param array $properties [reference]
	 * <p>The properties to process, as a set of <samp>name => value</samp> pairs.</p>
	 * @return void
	 */
	protected function processLogEventDataProperties(array &$properties): void {}
}
