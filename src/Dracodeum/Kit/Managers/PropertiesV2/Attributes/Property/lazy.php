<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/**
 * This attribute defines the property as lazy, so that a value is only validated and coerced later on read, 
 * instead of immediately on write.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class lazy implements IPropertyInitializer
{
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		$property->setAsLazy();
	}
}
