<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core text utility bulletify method options class.
 * 
 * @since 1.0.0
 * @property string $bullet [default = "\u{2022}"] <p>The bullet character to use.</p>
 * @see \Feralygon\Kit\Core\Utilities\Text
 */
class Bulletify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getDefaultPropertyValue(string $name)
	{
		switch ($name) {
			case 'bullet':
				return "\u{2022}";
		}
		return null;
	}
	
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'bullet':
				return UType::evaluateString($value) && UText::length($value, true) === 1;
		}
		return null;
	}
}
