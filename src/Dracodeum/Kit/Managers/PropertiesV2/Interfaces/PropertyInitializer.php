<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces;

use Dracodeum\Kit\Managers\PropertiesV2\Property;

interface PropertyInitializer
{
	//Public static methods
	/**
	 * Initialize a given property instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to initialize.
	 */
	public static function initializeProperty(Property $property): void;
}
