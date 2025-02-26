<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Log\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Primitives\Vector;
use Dracodeum\Kit\Utilities\Text\Options\Stringify as StringOptions;

/**
 * @property string|null $name [default = null]
 * <p>The name to log with.</p>
 * @property mixed $data [default = null]
 * <p>The data to log with.</p>
 * @property \Dracodeum\Kit\Primitives\Vector $tags [default = \Dracodeum\Kit\Primitives\Vector::build()]
 * <p>The tags vector instance to log with, with each value coerced into a string.</p>
 * @property string|null $function_name [default = null]
 * <p>The function or method name to use.<br>
 * If not set, then the name of the current function or method in the stack is used.</p>
 * @property int $stack_offset [default = 0]
 * <p>The stack offset to use.</p>
 * @property object|string|null $object_class [default = null]
 * <p>The object or class to use.<br>
 * If not set, then the object or class of the current function or method in the stack is used.</p>
 * @property array $parameters [default = []]
 * <p>The parameters to replace the given message placeholders with, as a set of <samp>name => value</samp> pairs.</p>
 * @property \Dracodeum\Kit\Utilities\Text\Options\Stringify $string_options [default = null]
 * <p>The text utility <code>Dracodeum\Kit\Utilities\Text</code> stringification method options to use for 
 * the given message.</p>
 * @property callable|null $stringifier [default = null]
 * <p>The function to use to stringify a given value for a given message placeholder.<br>
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
 * The stringified value for the given message placeholder or <code>null</code> if no stringification occurred.</p>
 */
class Event extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'name':
				//no break
			case 'function_name':
				return $this->createProperty()->setAsString(true, true)->setDefaultValue(null);
			case 'data':
				return $this->createProperty()->setDefaultValue(null);
			case 'tags':
				return $this->createProperty()->setAsVector(Vector::build()->setAsString(true))->setDefaultValue([]);
			case 'stack_offset':
				return $this->createProperty()->setAsInteger(true)->setDefaultValue(0);
			case 'object_class':
				return $this->createProperty()->setAsObjectClass(null, true)->setDefaultValue(null);
			case 'parameters':
				return $this->createProperty()->setAsArray()->setDefaultValue([]);
			case 'string_options':
				return $this->createProperty()->setAsOptions(StringOptions::class)->setDefaultValue(null);
			case 'stringifier':
				return $this->createProperty()
					->setAsCallable(function (string $placeholder, $value): ?string {}, true, true)
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
