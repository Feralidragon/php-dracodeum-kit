<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Time\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * Time utility <code>generate</code> method options.
 * 
 * @since 1.0.0
 * @property string|null $format [default = null]
 * <p>The values format to use, as supported by the PHP <code>date</code> function.<br>
 * If not set, no format is used for the values, therefore only values in seconds will be returned.<br>
 * If set, it cannot be empty.</p>
 * @property string|null $keys_format [default = null]
 * <p>The keys format to use, as supported by the PHP <code>date</code> function.<br>
 * If not set, no format is used for the keys, therefore only keys in seconds will be returned.<br>
 * If set, it cannot be empty.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see \Feralygon\Kit\Utilities\Time
 */
class Generate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'format':
				//no break
			case 'keys_format':
				return $this->createProperty()->setAsString(true, true)->setDefaultValue(null);
		}
		return null;
	}
}
