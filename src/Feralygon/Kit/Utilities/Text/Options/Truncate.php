<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Text\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Objects\Property;

/**
 * Text utility <code>truncate</code> method options class.
 * 
 * @since 1.0.0
 * @property bool $unicode [default = false] <p>Handle the string as Unicode.</p>
 * @property bool $ellipsis [default = false] <p>Add an ellipsis at the end of the truncated string.</p>
 * @property bool $keep_words [default = false] <p>Try to keep words preserved in the truncated string.</p>
 * @property bool $keep_sentences [default = false] <p>Try to keep sentences preserved in the truncated string.</p>
 * @property string|null $ellipsis_string [default = null] <p>The ellipsis string to use.<br>
 * If not set, the internal default ellipsis string is used.</p>
 * @see \Feralygon\Kit\Utilities\Text
 */
class Truncate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'unicode':
				//no break
			case 'ellipsis':
				//no break
			case 'keep_words':
				//no break
			case 'keep_sentences':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
			case 'ellipsis_string':
				return $this->createProperty()->setAsString(false, true)->setDefaultValue(null);
		}
		return null;
	}
}
