<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Text\Options;

use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;

/**
 * Core text utility mbulletify method options class.
 * 
 * @since 1.0.0
 * @property bool $merge [default = false] <p>Merge all the given strings into a single one, 
 * with each string in a new line.</p>
 * @property bool $punctuate [default = false] <p>Punctuate all the given strings with the appropriate symbols 
 * (a semicolon per line and period in the last one).</p>
 */
class Mbulletify extends Bulletify
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
	{
		switch ($name) {
			case 'merge':
				//no break
			case 'punctuate':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return parent::buildProperty($name);
	}
}
