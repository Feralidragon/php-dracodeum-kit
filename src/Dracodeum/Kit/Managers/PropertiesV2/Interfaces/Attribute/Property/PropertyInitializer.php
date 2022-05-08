<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Property;

/** This interface defines a method to initialize a property instance from a property attribute. */
interface PropertyInitializer
{
	//Public methods
	/**
	 * Initialize a given property instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to initialize.
	 */
	public function initializeProperty(Property $property): void;
}
