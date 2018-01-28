<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core time utility generate method options class.
 * 
 * @since 1.0.0
 * @property string|null $format [default = null] <p>The values format 
 * as supported by the PHP core <code>date</code> function.<br>
 * If not set, no format is used for the values, therefore only values in seconds will be returned.</p>
 * @property string|null $keys_format [default = null] <p>The keys format 
 * as supported by the PHP core <code>date</code> function.<br>
 * If not set, no format is used for the keys, therefore only keys in seconds will be returned.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Core\Utilities\Time
 */
class Generate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'format':
				//no break
			case 'keys_format':
				return UType::evaluateString($value, true);
		}
		return null;
	}
}
