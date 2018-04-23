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
 * Text utility <code>extract</code> method options.
 * 
 * @since 1.0.0
 * @property string[] $patterns [default = []]
 * <p>The regular expression patterns to use for each parameter, as <samp>placeholder => pattern</samp> pairs.</p>
 * @property string $pattern_modifiers [default = '']
 * <p>The regular expression pattern modifiers to use.</p>
 * @property string $pattern_delimiter [default = '/']
 * <p>The regular expression delimiter character to use.</p>
 * @property bool $no_throw [default = false]
 * <p>Do not throw an exception.</p>
 * @see \Feralygon\Kit\Utilities\Text
 */
class Extract extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'patterns':
				return $this->createProperty()
					->setAsArray(function (&$key, &$value) : bool {
						return UType::evaluateString($value);
					})
					->setDefaultValue([])
				;
			case 'pattern_modifiers':
				return $this->createProperty()
					->setAsString()
					->addEvaluator(function (&$value) : bool {
						return $value === '' || preg_match('/^[imsxADSUXJu]+$/', $value);
					})
					->setDefaultValue('')
				;
			case 'pattern_delimiter':
				return $this->createProperty()
					->setAsString(true)
					->addEvaluator(function (&$value) : bool {
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
