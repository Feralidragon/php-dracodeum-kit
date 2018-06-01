<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Memoization\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;

/**
 * @since 1.0.0
 * @property int|null $ttl [default = null]
 * <p>The TTL (Time to Live) of each memoized data entry, in seconds.<br>
 * If not set, then no TTL is applied, otherwise it must always be greater than <code>0</code>.</p>
 * @property int|null $limit [default = null]
 * <p>The limit on the number of memoized data entries.<br>
 * If not set, then no limit is applied, otherwise it must always be greater than <code>0</code>.</p>
 * @see https://en.wikipedia.org/wiki/Time_to_live
 * @see \Feralygon\Kit\Traits\Memoization
 */
final class Policy extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'ttl':
				//no break
			case 'limit':
				return $this->createProperty()
					->setAsInteger(true, null, true)
					->addEvaluator(function (&$value) : bool {
						return !isset($value) || $value > 0;
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
