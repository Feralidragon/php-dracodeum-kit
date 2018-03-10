<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Root\Locale;

/**
 * @since 1.0.0
 * @property int $info_scope [default = \Feralygon\Kit\Enumerations\InfoScope::NONE] <p>The info scope to use, 
 * as a value from the <code>Feralygon\Kit\Enumerations\InfoScope</code> enumeration.</p>
 * @property bool $translate [default = false] <p>Translate the returning text.</p>
 * @property string|null $language [default = null] <p>The language ISO 639 code to translate the returning text to.<br>
 * If not set, the currently set locale language is used.<br>
 * This property is only relevant if the property <var>$translate</var> above is set to <code>true</code>.</p>
 * @see https://en.wikipedia.org/wiki/ISO_639
 */
class Text extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'info_scope':
				return $this->createProperty()
					->setAsEnumerationValue(EInfoScope::class)
					->setDefaultValue(EInfoScope::NONE)
				;
			case 'translate':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'language':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return Locale::evaluateLanguage($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
}
