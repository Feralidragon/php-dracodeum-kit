<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Options\Traits;

/** This trait defines a method to extract properties from a string in an options class. */
trait StringPropertiesExtractor
{
	//Protected static methods
	/**
	 * Extract properties from a given string.
	 * 
	 * @param string $string
	 * <p>The string to extract from.</p>
	 * @return array|null
	 * <p>The extracted properties from the given string, as a set of <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none could be extracted.</p>
	 */
	protected static function extractStringProperties(string $string): ?array
	{
		return null;
	}
}
