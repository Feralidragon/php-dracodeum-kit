<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Call as UCall;

/**
 * Core text utility fill method options class.
 * 
 * @since 1.0.0
 * @property \Feralygon\Kit\Core\Utilities\Text\Options\Stringify|array|null $string_options [default = null] 
 * <p>The text utility <code>\Feralygon\Kit\Core\Utilities\Text</code> stringification method options, 
 * as an instance or <samp>name => value</samp> pairs.</p>
 * @property \Closure|null $stringifier [default = null] <p>The function to stringify a given value 
 * for a given placeholder.<br>
 * It is expected to be compatible with the following signature:<br><br>
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
class Fill extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getDefaultPropertyValue(string $name)
	{
		return null;
	}
	
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'string_options':
				return Stringify::evaluate($value);
			case 'stringifier':
				return UCall::evaluate($value, function (string $placeholder, $value) : ?string {}, true, true);
		}
		return null;
	}
}
