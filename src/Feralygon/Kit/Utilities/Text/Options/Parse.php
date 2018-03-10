<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Utilities\Type as UType;

/**
 * Text utility <code>parse</code> method options.
 * 
 * @since 1.0.0
 * @property string $delimiter_pattern [default = '\s+'] <p>The delimiter regular expression pattern to use to  
 * separate the fields patterns.</p>
 * @property string $pattern_modifiers [default = ''] <p>The regular expression pattern modifiers to use.</p>
 * @property string $pattern_delimiter [default = '/'] <p>The regular expression delimiter character to use.</p>
 * @see \Feralygon\Kit\Utilities\Text
 */
class Parse extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'delimiter_pattern':
				return $this->createProperty()->setAsString()->setDefaultValue('\s+');
			case 'pattern_modifiers':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value) && 
							($value === '' || (bool)preg_match('/^[imsxADSUXJu]+$/', $value));
					})
					->setDefaultValue('')
				;
			case 'pattern_delimiter':
				return $this->createProperty()
					->setEvaluator(function (&$value) : bool {
						return UType::evaluateString($value) && strlen($value) === 1;
					})
					->setDefaultValue('/')
				;
		}
		return null;
	}
}
