<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Json\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * JSON utility encode method options class.
 * 
 * @since 1.0.0
 * @property int $flags [default = 0x00] <p>The flags to use, 
 * as supported as <var>$options</var> by the PHP <code>json_encode</code> function.</p>
 * @property int|null $depth [default = null] <p>The depth to use, 
 * as supported as <var>$depth</var> by the PHP <code>json_encode</code> function.</p>
 * @see http://php.net/manual/en/function.json-encode.php
 * @see \Feralygon\Kit\Utilities\Json
 */
class Encode extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'flags':
				return $this->createProperty()->setAsStrictInteger()->setDefaultValue(0x00);
			case 'depth':
				return $this->createProperty()->setAsInteger(true)->setDefaultValue(null);
		}
		return null;
	}
}