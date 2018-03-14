<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * Text utility <code>fill</code> method options.
 * 
 * @since 1.0.0
 * @property \Feralygon\Kit\Utilities\Text\Options\Stringify|array|null $string_options [default = null]
 * <p>The text utility <code>Feralygon\Kit\Utilities\Text</code> stringification method options to use, 
 * as an instance or <samp>name => value</samp> pairs.</p>
 * @property \Closure|null $stringifier [default = null]
 * <p>The function to stringify a given value for a given placeholder.<br>
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
 * The stringified value for the given placeholder or <code>null</code> if no stringification occurred.</p>
 * @see \Feralygon\Kit\Utilities\Text
 */
class Fill extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'string_options':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return Stringify::evaluate($value);
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
