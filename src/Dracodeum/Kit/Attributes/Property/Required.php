<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Attributes\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer as IPropertyInitializer;
use Dracodeum\Kit\Managers\PropertiesV2\Property;
use Attribute;

/** This attribute defines the property as required, so that it is mandatory to be given during instantiation. */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required implements IPropertyInitializer
{
	//Implemented final public methods (Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property\PropertyInitializer)
	/** {@inheritdoc} */
	final public function initializeProperty(Property $property): void
	{
		$property->setAsRequired();
	}
}
