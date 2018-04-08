<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Utilities\Text\Options\Stringify as StringifyOptions;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * Call utility <code>guard</code> method options.
 * 
 * @since 1.0.0
 * @property string|null $error_message [default = null]
 * <p>The error message to use in the thrown exception, 
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
 * the identifiers are interpreted as getter method calls, but they cannot be given any custom parameters.</p>
 * @property string|null $hint_message [default = null]
 * <p>The hint message to use in the thrown exception, 
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
 * the identifiers are interpreted as getter method calls, but they cannot be given any custom parameters.</p>
 * @property string|null $function_name [default = null]
 * <p>The function or method name to use.<br>
 * If not set, the name of the current function or method in the stack is used.</p>
 * @property int $stack_offset [default = 0]
 * <p>The stack offset to use.<br>
 * It must be greater than or equal to <code>0</code>.</p>
 * @property object|string|null $object_class [default = null]
 * <p>The object or class to use.<br>
 * If not set, the object or class of the current function or method in the stack is used.</p>
 * @property array $parameters [default = []]
 * <p>The parameters to replace the hint message placeholders with, as <samp>name => value</samp> pairs.</p>
 * @property \Feralygon\Kit\Utilities\Text\Options\Stringify $string_options [default = null]
 * <p>The text utility <code>Feralygon\Kit\Utilities\Text</code> stringification method options to use for 
 * the hint message.</p>
 * @property \Closure|null $stringifier [default = null]
 * <p>The function to use to stringify a given value for a given hint message placeholder.<br>
 * It is expected to be compatible with the following signature:<br><br>
 * <code>function (string $placeholder, $value) : ?string</code><br>
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
 */
class Guard extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'error_message':
				//no break
			case 'hint_message':
				//no break
			case 'function_name':
				return $this->createProperty()->setAsString(false, true)->setDefaultValue(null);
			case 'stack_offset':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateInteger($value) && $value >= 0;
					})
					->setDefaultValue(0)
				;
			case 'object_class':
				return $this->createProperty()->setAsObjectClass(null, true)->setDefaultValue(null);
			case 'parameters':
				return $this->createProperty()->setAsArray()->setDefaultValue([]);
			case 'string_options':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return StringifyOptions::evaluate($value);
					})
					->setDefaultValue(null)
				;
			case 'stringifier':
				return $this->createProperty()
					->setAsCallable(function (string $placeholder, $value) : ?string {}, true, true)
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
