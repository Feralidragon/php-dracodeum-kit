<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core text utility truncate method options class.
 * 
 * @since 1.0.0
 * @property bool $unicode [default = false] <p>Handle the string as Unicode.</p>
 * @property bool $ellipsis [default = false] <p>Add an ellipsis at the end of the truncated string.</p>
 * @property bool $keep_words [default = false] <p>Try to keep words preserved in the truncated string.</p>
 * @property bool $keep_sentences [default = false] <p>Try to keep sentences preserved in the truncated string.</p>
 * @property string|null $ellipsis_string [default = null] <p>The ellipsis string to use.<br>
 * If not set, the internal default ellipsis string is used.</p>
 * @see \Feralygon\Kit\Core\Utilities\Text
 */
class Truncate extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'unicode':
				//no break
			case 'ellipsis':
				//no break
			case 'keep_words':
				//no break
			case 'keep_sentences':
				$value = $value ?? false;
				return UType::evaluateBoolean($value);
			case 'ellipsis_string':
				return UType::evaluateString($value, true);
		}
		return null;
	}
}
