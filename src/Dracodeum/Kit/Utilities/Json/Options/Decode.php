<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Json\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * JSON utility <code>decode</code> method options.
 * 
 * @property bool $associative [coercive] [default = false]
 * <p>Decode into associative arrays instead of objects, 
 * as supported as <var>$assoc</var> by the PHP <code>json_decode</code> function.</p>
 * @property int $flags [strict] [default = 0x00]
 * <p>The flags to use, as supported as <var>$options</var> by the PHP <code>json_decode</code> function.</p>
 * @property int|null $depth [coercive] [default = null]
 * <p>The depth to use, as supported as <var>$depth</var> by the PHP <code>json_decode</code> function.</p>
 * @property bool $no_throw [coercive] [default = false]
 * <p>Do not throw an exception.</p>
 * @see http://php.net/manual/en/function.json-decode.php
 */
class Decode extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'associative':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
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
