<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\Locale\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Text\Options\Stringify as StringOptions;
use Feralygon\Kit\Root\Locale;

/**
 * Root locale translate method options class.
 * 
 * @since 1.0.0
 * @property array $parameters [default = []] <p>The parameters to replace the respective message placeholders with, 
 * as <samp>name => value</samp> pairs.</p>
 * @property int $info_scope [default = \Feralygon\Kit\Core\Enumerations\InfoScope::NONE] <p>The info scope to use.</p>
 * @property string|null $language [default = null] <p>The language ISO 639 code to translate the message to.<br>
 * If not set, the currently set locale language is used.</p>
 * @property \Feralygon\Kit\Core\Utilities\Text\Options\Stringify|array|null $string_options [default = null] 
 * <p>The text utility <code>Feralygon\Kit\Core\Utilities\Text</code> stringification method options, 
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
 * @see https://en.wikipedia.org/wiki/ISO_639
 * @see \Feralygon\Kit\Root\Locale
 */
class Translate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'parameters':
				return $this->createProperty()->setAsArray()->setDefaultValue([]);
			case 'info_scope':
				return $this->createProperty()
					->setAsEnumerationValue(EInfoScope::class)
					->setDefaultValue(EInfoScope::NONE)
				;
			case 'language':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return Locale::evaluateLanguage($value, true);
					})
					->setDefaultValue(null)
				;
			case 'string_options':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return StringOptions::evaluate($value);
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
