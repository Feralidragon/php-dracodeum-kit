<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Text\Options\Stringify as StringOptions;

/**
 * Call utility <code>guard</code> method options.
 * 
 * @property string|null $error_message [coercive] [default = null]
 * <p>The error message to use in the thrown exception, 
 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
 * If set, then placeholders must be exclusively composed by identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, 
 * within the set properties, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
 * with no limit on the number of chained pointers.<br>
 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
 * then the identifiers are interpreted as getter method calls, but they cannot be given any custom parameters.</p>
 * @property string|null $error_message_plural [coercive] [default = null]
 * <p>The plural version of the <var>$error_message</var> above to use in the thrown exception.</p>
 * @property float|null $error_message_number [coercive] [default = null]
 * <p>The number to use to select either the singular (<var>$error_message</var>) 
 * or plural (<var>$error_message_plural</var>) version of the error message to use in the thrown exception.</p>
 * @property string|null $error_message_number_placeholder [coercive] [default = null]
 * <p>The placeholder to fill with the given <var>$error_message_number</var> above in the error message 
 * (<var>$error_message</var> and <var>$error_message_plural</var>).</p>
 * @property string|null $hint_message [coercive] [default = null]
 * <p>The hint message to use in the thrown exception, 
 * optionally set with placeholders as <samp>{{placeholder}}</samp>.<br>
 * If set, then placeholders must be exclusively composed by identifiers, 
 * which are defined as words which must start with a letter (<samp>a-z</samp> and <samp>A-Z</samp>) 
 * or underscore (<samp>_</samp>), and may only contain letters (<samp>a-z</samp> and <samp>A-Z</samp>), 
 * digits (<samp>0-9</samp>) and underscores (<samp>_</samp>).<br>
 * <br>
 * They may also be used with pointers to specific object properties or associative array values, 
 * within the set properties, by using a dot between identifiers, such as <samp>{{object.property}}</samp>, 
 * with no limit on the number of chained pointers.<br>
 * If suffixed with opening and closing parenthesis, such as <samp>{{object.method()}}</samp>, 
 * then the identifiers are interpreted as getter method calls, but they cannot be given any custom parameters.</p>
 * @property string|null $hint_message_plural [coercive] [default = null]
 * <p>The plural version of the <var>$hint_message</var> above to use in the thrown exception.</p>
 * @property float|null $hint_message_number [coercive] [default = null]
 * <p>The number to use to select either the singular (<var>$hint_message</var>) 
 * or plural (<var>$hint_message_plural</var>) version of the hint message to use in the thrown exception.</p>
 * @property string|null $hint_message_number_placeholder [coercive] [default = null]
 * <p>The placeholder to fill with the given <var>$hint_message_number</var> above in the hint message 
 * (<var>$hint_message</var> and <var>$hint_message_plural</var>).</p>
 * @property string|null $function_name [coercive] [default = null]
 * <p>The function or method name to use.<br>
 * If not set, then the name of the current function or method in the stack is used.</p>
 * @property int $stack_offset [coercive] [default = 0]
 * <p>The stack offset to use.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property object|string|null $object_class [coercive] [default = null]
 * <p>The object or class to use.<br>
 * If not set, then the object or class of the current function or method in the stack is used.</p>
 * @property array $parameters [coercive] [default = []]
 * <p>The parameters to replace the hint message placeholders with, as <samp>name => value</samp> pairs.</p>
 * @property \Feralygon\Kit\Utilities\Text\Options\Stringify $string_options [coercive = options] [default = null]
 * <p>The text utility <code>Feralygon\Kit\Utilities\Text</code> stringification method options to use for 
 * the hint message.</p>
 * @property callable|null $stringifier [coercive] [default = null]
 * <p>The function to use to stringify a given value for a given hint message placeholder.<br>
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
 * The stringified value for the given hint message placeholder or <code>null</code> if no stringification occurred.</p>
 * @see \Feralygon\Kit\Utilities\Call
 * @see \Feralygon\Kit\Utilities\Text
 */
class Guard extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'error_message':
				//no break
			case 'error_message_plural':
				//no break
			case 'error_message_number_placeholder':
				//no break
			case 'hint_message':
				//no break
			case 'hint_message_plural':
				//no break
			case 'hint_message_number_placeholder':
				//no break
			case 'function_name':
				return $this->createProperty()->setAsString(false, true)->setDefaultValue(null);
			case 'error_message_number':
				//no break
			case 'hint_message_number':
				return $this->createProperty()->setAsFloat(true)->setDefaultValue(null);
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
