<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Url\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * URL utility <code>querify</code> method options.
 * 
 * @property string $delimiter [default = '&']
 * <p>The delimiter to use between key-value pairs.</p>
 * @property bool $allow_arrays [default = false]
 * <p>Allow array values to be querified.</p>
 * @property bool $no_encode [default = false]
 * <p>Do not encode the keys nor the values.</p>
 */
class Querify extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'delimiter':
				return $this->createProperty()
					->setAsString(true)
					->addEvaluator(function (&$value): bool {
						return strlen($value) === 1;
					})
					->setDefaultValue('&')
				;
			case 'allow_arrays':
				//no break
			case 'no_encode':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
