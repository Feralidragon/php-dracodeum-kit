<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Property;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Managers\PropertiesV2\Property;

/**
 * @property-read \Dracodeum\Kit\Managers\PropertiesV2\Property $property
 * The property instance.
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class);
	}
}
