<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Core text utility localize method options class.
 * 
 * @since 1.0.0
 * @property array $parameters [default = []] <p>The parameters to replace the respective message placeholders with, as <samp>name => value</samp> pairs.</p>
 * @property int $string_flags [default = 0x00] <p>The text utility <code>\Feralygon\Kit\Core\Utilities\Text</code> class stringification bitwise flags, 
 * which can be any combination of the following:<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NO_QUOTES</code> : Do not add quotes to strings in the returning string.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_PREPEND_TYPE</code> : Always prepend the type for every value in the returning string.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_CONJUNCTION_OR</code> : Use an "or" conjunction in the returning string for non-associative arrays.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_CONJUNCTION_NOR</code> : Use a "nor" conjunction in the returning string for non-associative arrays.<br><br>
 * &nbsp; &#8226; &nbsp; <code>STRING_NONASSOC_CONJUNCTION_AND</code> : Use an "and" conjunction in the returning string for non-associative arrays.
 * </p>
 * @property \Closure|null $stringifier [default = null] <p>The function to stringify a given value for a given placeholder.<br>
 * The expected function signature is represented as:<br><br>
 * <code>function (string $placeholder, $value) : ?string</code><br>
 * <br>
 * Parameters:<br>
 * &nbsp; &#8226; &nbsp; <code><b>string $placeholder</b></code> : The placeholder to stringify for.<br>
 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code> : The value to stringify.<br>
 * <br>
 * Return: <code><b>string|null</b></code><br>
 * The stringified value for the given placeholder or <code>null</code> if no stringification occurred.
 * </p>
 * @see \Feralygon\Kit\Core\Utilities\Text
 */
class Localize extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'parameters':
				$value = $value ?? [];
				return is_array($value);
			case 'string_flags':
				$value = $value ?? 0x00;
				return UType::evaluateInteger($value);
			case 'stringifier':
				return UCall::evaluate($value, function (string $placeholder, $value) : ?string {}, true);
		}
		return null;
	}
}
