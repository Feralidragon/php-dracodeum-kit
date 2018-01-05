<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Memoization\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core memoization trait policy options class.
 * 
 * @since 1.0.0
 * @property int|null $ttl [default = null] <p>The TTL (Time to Live) of each memoized data entry, in seconds.<br>
 * If not set, no TTL is applied, otherwise it must always be greater than <code>0</code>.</p>
 * @property int|null $limit [default = null] <p>The limit on the number of memoized data entries.<br>
 * If not set, no limit is applied, otherwise it must always be greater than <code>0</code>.</p>
 * @see https://en.wikipedia.org/wiki/Time_to_live
 * @see \Feralygon\Kit\Core\Traits\Memoization
 */
final class Policy extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'ttl':
				//no break
			case 'limit':
				return !isset($value) || (UType::evaluateInteger($value) && $value > 0);
		}
		return null;
	}
}
