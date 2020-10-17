<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Options;

use Dracodeum\Kit\Options;
use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Text utility <code>parse</code> method options.
 * 
 * @property string $delimiter_pattern [default = '\s+']
 * <p>The delimiter regular expression pattern to use to separate the fields patterns.</p>
 * @property string $pattern_modifiers [default = '']
 * <p>The regular expression pattern modifiers to use.</p>
 * @property string $pattern_delimiter [default = '/']
 * <p>The regular expression delimiter character to use.</p>
 * @property bool $no_throw [default = false]
 * <p>Do not throw an exception.</p>
 */
class Parse extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'delimiter_pattern':
				return $this->createProperty()->setAsString()->setDefaultValue('\s+');
			case 'pattern_modifiers':
				return $this->createProperty()
					->setAsString()
					->addEvaluator(function (&$value): bool {
						return $value === '' || preg_match('/^[imsxADSUXJu]+$/', $value);
					})
					->setDefaultValue('')
				;
			case 'pattern_delimiter':
				return $this->createProperty()
					->setAsString(true)
					->addEvaluator(function (&$value): bool {
						return strlen($value) === 1;
					})
					->setDefaultValue('/')
				;
			case 'no_throw':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
