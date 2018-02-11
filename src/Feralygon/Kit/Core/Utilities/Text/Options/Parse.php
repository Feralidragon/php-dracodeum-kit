<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility parse method options class.
 * 
 * @since 1.0.0
 * @property string $delimiter_pattern [default = '\s+'] <p>The delimiter regular expression pattern 
 * which separates the fields patterns.</p>
 * @property string $pattern_modifiers [default = ''] <p>The regular expression pattern modifiers.</p>
 * @property string $pattern_delimiter [default = '/'] <p>The regular expression delimiter character.</p>
 * @see \Feralygon\Kit\Core\Utilities\Text
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
