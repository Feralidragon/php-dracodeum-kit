<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Inputs\Date\Prototypes\Modifiers\Filters;

use Feralygon\Kit\Components\Input\Prototypes\Modifiers\Filter;
use Feralygon\Kit\Utilities\Time as UTime;

/**
 * This filter prototype converts a date into a string using the ISO-8601 format.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/ISO_8601
 * @see \Feralygon\Kit\Prototypes\Inputs\Date
 */
class Iso8601 extends Filter
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function processValue(&$value): bool
	{
		return UTime::evaluateDate($value, 'Y-m-d');
	}
}
