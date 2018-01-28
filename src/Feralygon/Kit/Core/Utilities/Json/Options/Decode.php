<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Json\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core JSON utility decode method options class.
 * 
 * @since 1.0.0
 * @property bool $associative [default = false] <p>Decode into associative arrays instead of objects, 
 * as supported as <var>$assoc</var> by the PHP core <code>json_decode</code> function.</p>
 * @property int $flags [default = 0x00] <p>The flags to use, 
 * as supported as <var>$options</var> by the PHP core <code>json_decode</code> function.</p>
 * @property int|null $depth [default = null] <p>The depth to use, 
 * as supported as <var>$depth</var> by the PHP core <code>json_decode</code> function.</p>
 * @see http://php.net/manual/en/function.json-decode.php
 * @see \Feralygon\Kit\Core\Utilities\Json
 */
class Decode extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'associative':
				$value = $value ?? false;
				return UType::evaluateBoolean($value);
			case 'flags':
				$value = $value ?? 0x00;
				return UType::evaluateInteger($value);
			case 'depth':
				return UType::evaluateInteger($value, true);
		}
		return null;
	}
}
