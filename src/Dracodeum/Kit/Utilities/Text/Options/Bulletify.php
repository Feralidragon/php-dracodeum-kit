<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * Text utility <code>bulletify</code> method options.
 * 
 * @property string $bullet [default = "\u{2022}"]
 * <p>The bullet character to use.</p>
 */
class Bulletify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'bullet':
				return $this->createProperty()
					->setAsString(true)
					->addEvaluator(function (&$value): bool {
						return UText::length($value, true) === 1;
					})
					->setDefaultValue("\u{2022}")
				;
		}
		return null;
	}
}
