<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Locale\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Enumerations\InfoScope as EInfoScope;
use Dracodeum\Kit\Utilities\Text\Options\Stringify as StringOptions;
use Dracodeum\Kit\Root\Locale;

/**
 * Root locale <code>translate</code> method options.
 * 
 * @property array $parameters [default = []]
 * <p>The parameters to replace the respective message placeholders with, 
 * as a set of <samp>name => value</samp> pairs.</p>
 * @property int $info_scope [default = INTERNAL]
 * <p>The info scope to use, as a value from the <code>Dracodeum\Kit\Enumerations\InfoScope</code> enumeration.</p>
 * @property string|null $language [default = null]
 * <p>The language ISO 639 code to translate the message to.<br>
 * If not set, then the currently set locale language is used.</p>
 * @property \Dracodeum\Kit\Utilities\Text\Options\Stringify $string_options [default = auto]
 * <p>The text utility <code>Dracodeum\Kit\Utilities\Text</code> stringification method options to use.</p>
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
 * @see https://en.wikipedia.org/wiki/ISO_639
 * @see \Dracodeum\Kit\Enumerations\InfoScope
 * @see \Dracodeum\Kit\Utilities\Text
 */
class Translate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'parameters':
				return $this->createProperty()->setAsArray()->setDefaultValue([]);
			case 'info_scope':
				return $this->createProperty()
					->setAsEnumerationValue(EInfoScope::class)
					->setDefaultValue(EInfoScope::INTERNAL)
				;
			case 'language':
				return $this->createProperty()
					->addEvaluator(function (&$value): bool {
						return Locale::evaluateLanguage($value, true);
					})
					->setDefaultValue(null)
				;
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
