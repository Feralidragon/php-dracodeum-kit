<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\Log\Options;

use Dracodeum\Kit\Traits\LazyProperties\Property;

/**
 * @property string|null $number_placeholder [coercive] [default = null]
 * <p>The placeholder to fill with the given number in the final message.<br>
 * If set, then it cannot be empty.</p>
 */
class PEvent extends Event
{
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name): ?Property
	{
		switch ($name) {
			case 'number_placeholder':
				return $this->createProperty()->setAsString(true, true)->setDefaultValue(null);
		}
		return parent::buildProperty($name);
	}
}
