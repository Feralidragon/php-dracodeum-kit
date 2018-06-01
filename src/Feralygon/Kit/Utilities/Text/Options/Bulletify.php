<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * Text utility <code>bulletify</code> method options.
 * 
 * @since 1.0.0
 * @property string $bullet [default = "\u{2022}"]
 * <p>The bullet character to use.</p>
 * @see \Feralygon\Kit\Utilities\Text
 */
class Bulletify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'bullet':
				return $this->createProperty()
					->setAsString(true)
					->addEvaluator(function (&$value) : bool {
						return UText::length($value, true) === 1;
					})
					->setDefaultValue("\u{2022}")
				;
		}
		return null;
	}
}
