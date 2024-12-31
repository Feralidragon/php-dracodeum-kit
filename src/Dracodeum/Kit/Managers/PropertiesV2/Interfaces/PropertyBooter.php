<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces;

use Dracodeum\Kit\Managers\PropertiesV2\Property;

interface PropertyBooter
{
	//Public static methods
	/**
	 * Boot a given property instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to boot.
	 */
	public static function bootProperty(Property $property): void;
}
