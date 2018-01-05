<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Enumerations\InfoScope as EInfoScope;
use Feralygon\Kit\Core\Utilities\Type as UType;
use Feralygon\Kit\Root\Locale;

/**
 * Core text options class.
 * 
 * @since 1.0.0
 * @property int $info_scope [default = \Feralygon\Kit\Core\Enumerations\InfoScope::NONE] <p>The info scope to use.</p>
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
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'info_scope':
				$value = $value ?? EInfoScope::NONE;
				return EInfoScope::evaluateValue($value);
			case 'translate':
				$value = $value ?? false;
				return UType::evaluateBoolean($value);
			case 'language':
				return Locale::evaluateLanguage($value, true);
		}
		return null;
	}
}
