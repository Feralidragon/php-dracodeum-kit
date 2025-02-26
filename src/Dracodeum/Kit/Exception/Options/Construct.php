<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Exception\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Exception <code>construct</code> method options.
 * 
 * @property string|null $message [default = null]
 * <p>The message, optionally set with placeholders as <samp>{{placeholder}}</samp>, corresponding directly to given 
 * properties.<br>
 * <br>
 * If set, then placeholders must be exclusively composed of identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, 
 * by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
 * with no limit on the number of chained pointers.<br>
 * <br>
 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
 * then the identifiers are interpreted as getter method calls, but they cannot be given any arguments.</p>
 * @property callable|null $stringifier [default = null]
 * <p>The function to use to stringify a given value for a given placeholder.<br>
 * It is expected to be compatible with the following signature:<br>
 * <br>
 * <code>function (string $placeholder, $value): ?string</code><br>
 * <br>
 * Parameters:<br>
 * &nbsp; &#8226; &nbsp; <code><b>string $placeholder</b></code><br>
 * &nbsp; &nbsp; &nbsp; The placeholder to stringify for.<br>
 * &nbsp; &#8226; &nbsp; <code><b>mixed $value</b></code><br>
 * &nbsp; &nbsp; &nbsp; The value to stringify.<br>
 * <br>
 * Return: <code><b>string|null</b></code><br>
 * The stringified value for the given placeholder or <code>null</code> if no stringification occurred.</p>
 * @property int|null $code [default = null]
 * <p>The numeric code.</p>
 * @property \Throwable|null $previous [default = null]
 * <p>The previous throwable instance.</p>
 */
class Construct extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'message':
				return $this->createProperty()->setAsString(false, true)->setDefaultValue(null);
			case 'stringifier':
				return $this->createProperty()
					->setAsCallable(function (string $placeholder, $value): ?string {}, true, true)
					->setDefaultValue(null)
				;
			case 'code':
				return $this->createProperty()->setAsStrictInteger(false, null, true)->setDefaultValue(null);
			case 'previous':
				return $this->createProperty()->setAsStrictObject(\Throwable::class, true)->setDefaultValue(null);
		}
		return null;
	}
}
