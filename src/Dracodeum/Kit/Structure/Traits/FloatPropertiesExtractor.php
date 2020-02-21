<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structure\Traits;

/** This trait defines a method to extract properties from a float in a structure. */
trait FloatPropertiesExtractor
{
	//Protected static methods
	/**
	 * Extract properties from a given float.
	 * 
	 * @param float $float
	 * <p>The float to extract from.</p>
	 * @return array|null
	 * <p>The extracted properties from the given float, as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none could be extracted.</p>
	 */
	protected static function extractFloatProperties(float $float): ?array
	{
		return null;
	}
}
