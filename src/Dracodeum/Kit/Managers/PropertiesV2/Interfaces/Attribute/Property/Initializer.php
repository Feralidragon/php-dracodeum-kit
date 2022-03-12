<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces\Attribute\Property;

use Dracodeum\Kit\Managers\PropertiesV2\Property;

/** This interface defines a method to initialize a property from an attribute. */
interface Initializer
{
	//Public methods
	/**
	 * Initialize a given property instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to initialize.
	 * 
	 * @return void
	 */
	public function initializeProperty(Property $property): void;
}
