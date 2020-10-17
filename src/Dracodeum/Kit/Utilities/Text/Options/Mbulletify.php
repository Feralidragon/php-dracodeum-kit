<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Text\Options;

use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * Text utility <code>mbulletify</code> method options.
 * 
 * @property bool $merge [default = false]
 * <p>Merge all the given strings into a single one, with each string in a new line.</p>
 * @property bool $punctuate [default = false]
 * <p>Punctuate each given string with the appropriate symbol, 
 * with a period in the last one and a semicolon in the others.</p>
 * @property bool $append_newline [default = false]
 * <p>Append an extra newline to each given string.</p>
 * @property bool $multiline_newline_append [default = false]
 * <p>Append an extra newline to each given multiline string.</p>
 */
class Mbulletify extends Bulletify
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'merge':
				//no break
			case 'punctuate':
				//no break
			case 'append_newline':
				//no break
			case 'multiline_newline_append':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return parent::buildProperty($name);
	}
}
