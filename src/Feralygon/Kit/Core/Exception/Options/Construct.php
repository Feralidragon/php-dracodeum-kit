<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Exception\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\{
	Call as UCall,
	Type as UType
};

/**
 * Core exception construct method options class.
 * 
 * @since 1.0.0
 * @property string|null $message [default = null] <p>The message, optionally set with placeholders as <code>{{placeholder}}</code>.<br>
 * If set, placeholders must be exclusively composed by identifiers, which are defined as words which must start with a letter (<code>a-z</code> and <code>A-Z</code>) 
 * or underscore (<code>_</code>), and may only contain letters (<code>a-z</code> and <code>A-Z</code>), digits (<code>0-9</code>) and underscores (<code>_</code>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, within the set properties, by using a dot between identifiers, 
 * such as <code>{{object.property}}</code>, with no limit on the number of chained pointers.<br>
 * If suffixed with opening and closing parenthesis, such as <code>{{object.method()}}</code>, the identifiers are interpreted as getter method calls, 
 * but they cannot be given any custom parameters.</p>
 * @property \Closure|null $stringifier [default = null] <p>The function to stringify a given value for a given placeholder.<br>
 * The expected function signature is represented as:<br><br>
 * <code>function (string $placeholder, $value) : ?string</code><br>
 * <br>
 * Parameters:<br>
 * &nbsp; &#8226; &nbsp; <code><b>string $placeholder</b></code> : The placeholder to stringify for.<br>
 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code> : The value to stringify.<br>
 * <br>
 * Return: <samp><b>string|null</b></samp><br>
 * The stringified value for the given placeholder or <samp>null</samp> if no stringification occurred.
 * </p>
 * @property int|null $code [default = null] <p>The numeric code.</p>
 * @property \Throwable|null $previous [default = null] <p>The previous throwable instance.</p>
 * @see \Feralygon\Kit\Core\Exception
 */
class Construct extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'message':
				return UType::evaluateString($value, true);
			case 'stringifier':
				return UCall::evaluate($value, function (string $placeholder, $value) : ?string {}, true);
			case 'code':
				return UType::evaluateInteger($value, true);
			case 'previous':
				return !isset($value) || (is_object($value) && UType::isA($value, \Throwable::class));
		}
		return null;
	}
}
