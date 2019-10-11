<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Options;

use Feralygon\Kit\Options;
use Feralygon\Kit\Traits\LazyProperties\Property;

/**
 * @property bool $exclude_null [coercive] [default = false]
 * <p>Exclude null information.</p>
 * @property bool $exclude_modifiers [coercive] [default = false]
 * <p>Exclude modifiers information.</p>
 */
class Info extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'exclude_null':
				//no break
			case 'exclude_modifiers':
				return $this->createProperty()->setAsBoolean()->setDefaultValue(false);
		}
		return null;
	}
}
