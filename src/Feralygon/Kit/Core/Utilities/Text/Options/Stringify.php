<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility stringify method options class.
 * 
 * @since 1.0.0
 * @property int $flags [default = 0x00] <p>The stringification bitwise flags, which can be any combination of the following:<br><br>
 * &nbsp; &#8226; &nbsp; <code>self::STRING_NO_QUOTES</code> : Do not add quotes to strings in the returning string.<br><br>
 * &nbsp; &#8226; &nbsp; <code>self::STRING_PREPEND_TYPE</code> : Always prepend the type for every value in the returning string.<br><br>
 * &nbsp; &#8226; &nbsp; <code>self::STRING_NONASSOC_CONJUNCTION_OR</code> : Use an "or" conjunction in the returning string for non-associative arrays.<br><br>
 * &nbsp; &#8226; &nbsp; <code>self::STRING_NONASSOC_CONJUNCTION_NOR</code> : Use a "nor" conjunction in the returning string for non-associative arrays.<br><br>
 * &nbsp; &#8226; &nbsp; <code>self::STRING_NONASSOC_CONJUNCTION_AND</code> : Use an "and" conjunction in the returning string for non-associative arrays.
 * </p>
 * @see \Feralygon\Kit\Core\Utilities\Text
 */
class Stringify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'flags':
				$value = $value ?? 0x00;
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
