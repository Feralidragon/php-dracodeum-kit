<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structure\Traits;

/** This trait defines a method to extract properties from an integer in a structure. */
trait IntegerPropertiesExtractor
{
	//Protected static methods
	/**
	 * Extract properties from a given integer.
	 * 
	 * @param int $integer
	 * <p>The integer to extract from.</p>
	 * @return array|null
	 * <p>The extracted properties from the given integer, as a set of <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none could be extracted.</p>
	 */
	protected static function extractIntegerProperties(int $integer): ?array
	{
		return null;
	}
}
