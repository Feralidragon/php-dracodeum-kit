<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Options;

use Feralygon\Kit\Core\Options;
use Feralygon\Kit\Core\Traits\LazyProperties\Objects\Property;

/**
 * Core input component info options class.
 * 
 * @since 1.0.0
 * @property bool $exclude_null [default = false] <p>Exclude null information.</p>
 * @property bool $exclude_modifiers [default = false] <p>Exclude modifiers information.</p>
 * @see \Feralygon\Kit\Core\Components\Input
 */
class Info extends Options
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperty(string $name) : ?Property
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
