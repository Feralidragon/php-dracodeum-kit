<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Factories\Options as Factory;
use Feralygon\Kit\Root\Locale;

/**
 * @property int $info_scope [coercive = enumeration value] [default = \Feralygon\Kit\Enumerations\InfoScope::NONE]
 * <p>The info scope to use, as a value from the <code>Feralygon\Kit\Enumerations\InfoScope</code> enumeration.</p>
 * @property bool $translate [coercive] [default = false]
 * <p>Translate the returning text.</p>
 * @property string|null $language [coercive = language] [default = null]
 * <p>The language ISO 639 code to translate the returning text to.<br>
 * If not set, then the currently set locale language is used.<br>
 * This property is only relevant if the property <var>$translate</var> above is set to <code>true</code>.</p>
 * @see https://en.wikipedia.org/wiki/ISO_639
 * @see \Feralygon\Kit\Enumerations\InfoScope
 */
class Text extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
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
					->addEvaluator(function (&$value): bool {
						return Locale::evaluateLanguage($value, true);
					})
					->setDefaultValue(null)
				;
		}
		return null;
	}
	
	
	
	//Implemented protected static methods (Feralygon\Kit\Options\Traits\DefaultBuilder)
	/** {@inheritdoc} */
	protected static function getDefaultBuilder(): ?callable
	{
		return [Factory::class, 'text'];
	}
}
