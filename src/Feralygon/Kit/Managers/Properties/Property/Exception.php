<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Properties\Property;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Properties\Property;

/**
 * @property-read \Feralygon\Kit\Managers\Properties\Property $property [strict]
 * <p>The property instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class);
	}
}
