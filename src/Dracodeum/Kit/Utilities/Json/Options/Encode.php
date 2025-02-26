<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Json\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * JSON utility <code>encode</code> method options.
 * 
 * @property int $flags [default = 0x00]
 * <p>The flags to use, as supported as <var>$options</var> by the PHP <code>json_encode</code> function.</p>
 * @property int|null $depth [default = null]
 * <p>The depth to use, as supported as <var>$depth</var> by the PHP <code>json_encode</code> function.</p>
 * @property bool $no_throw [default = false]
 * <p>Do not throw an exception.</p>
 * @see http://php.net/manual/en/function.json-encode.php
 */
class Encode extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'flags':
				return $this->createProperty()->setAsStrictInteger()->setDefaultValue(0x00);
			case 'depth':
				return $this->createProperty()->setAsInteger(false, null, true)->setDefaultValue(null);
			case 'no_throw':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
