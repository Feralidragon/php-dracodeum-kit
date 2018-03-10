<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\DateTime\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers\Filter;

/**
 * This filter prototype converts a date and time, as an Unix timestamp, into a string using an ISO-8601 format.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see \Feralygon\Kit\Prototypes\Inputs\DateTime
 */
class Iso8601 extends Filter
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value) : bool
	{
		if (is_int($value)) {
			$value = date('c', $value);
			return $value !== false;
		}
		return false;
	}
}
