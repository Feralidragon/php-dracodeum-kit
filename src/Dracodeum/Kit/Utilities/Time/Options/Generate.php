<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Time\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Time utility <code>generate</code> method options.
 * 
 * @property string|null $format [default = null]
 * <p>The values format to use, as supported by the PHP <code>date</code> function, 
 * or as a <code>DateTime</code> or <code>DateTimeImmutable</code> class to instantiate.<br>
 * If not set, then no format is used for the values, therefore only values in seconds will be returned.</p>
 * @property string|null $timezone [default = null]
 * <p>The values timezone to use, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
 * If not set, then the currently set default timezone is used for the values.</p>
 * @property string|null $keys_format [default = null]
 * <p>The keys format to use, as supported by the PHP <code>date</code> function.<br>
 * If not set, then no format is used for the keys, therefore only keys in seconds will be returned.</p>
 * @property string|null $keys_timezone [default = null]
 * <p>The keys timezone to use, as supported by the PHP <code>date_default_timezone_set</code> function.<br>
 * If not set, then the currently set default timezone is used for the keys.</p>
 * @see https://php.net/manual/en/function.date.php
 * @see https://php.net/manual/en/function.date-default-timezone-set.php
 * @see https://php.net/manual/en/class.datetime.php
 * @see https://php.net/manual/en/class.datetimeimmutable.php
 */
class Generate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'format':
				//no break
			case 'timezone':
				//no break
			case 'keys_format':
				//no break
			case 'keys_timezone':
				return $this->createProperty()->setAsString(true, true)->setDefaultValue(null);
		}
		return null;
	}
}
