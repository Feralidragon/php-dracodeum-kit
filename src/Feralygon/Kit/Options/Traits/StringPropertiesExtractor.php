<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options\Traits;

/** 
 * This trait defines a method to extract properties from a string in an options class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Options
 */
trait StringPropertiesExtractor
{
	//Protected static methods
	/**
	 * Extract properties from a given string.
	 * 
	 * @since 1.0.0
	 * @param string $string
	 * <p>The string to extract from.</p>
	 * @return array|null
	 * <p>The extracted properties from the given string, as <samp>name => value</samp> pairs, 
	 * or <code>null</code> if none could be extracted.</p>
	 */
	protected static function extractStringProperties(string $string): ?array
	{
		return null;
	}
}
