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
 * @property string|null $message [default = null] <p>The message, 
 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
 * If set, placeholders must be exclusively composed by identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, 
 * within the set properties, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
 * with no limit on the number of chained pointers.<br>
 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
 * the identifiers are interpreted as getter method calls, 
 * but they cannot be given any custom parameters.</p>
 * @property \Closure|null $stringifier [default = null] <p>The function to stringify a given value 
 * for a given placeholder.<br>
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
 * @property int|null $code [default = null] <p>The numeric code.</p>
 * @property \Throwable|null $previous [default = null] <p>The previous throwable instance.</p>
 * @see \Feralygon\Kit\Core\Exception
 */
class Construct extends Options
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
			case 'message':
				return UType::evaluateString($value, true, true);
			case 'stringifier':
				return UCall::evaluate($value, function (string $placeholder, $value) : ?string {}, true, true);
			case 'code':
				return UType::evaluateInteger($value, true);
			case 'previous':
				return !isset($value) || (is_object($value) && UType::isA($value, \Throwable::class));
		}
		return null;
	}
}
