<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Time\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;

/**
 * This filter prototype converts a time, as an Unix timestamp, into a string using an ISO-8601 format.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see \Feralygon\Kit\Prototypes\Inputs\Time
 */
class Iso8601 extends Filter
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		if (is_int($value)) {
			$value = date('H:i:sP', $value);
			return $value !== false;
		}
		return false;
	}
}
