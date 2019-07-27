<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Hash\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;

/**
 * This filter prototype converts a hash to uppercase.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Prototypes\Inputs\Hash
 */
class Uppercase extends Filter
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (is_string($value)) {
			$value = strtoupper($value);
			return true;
		}
		return false;
	}
}
