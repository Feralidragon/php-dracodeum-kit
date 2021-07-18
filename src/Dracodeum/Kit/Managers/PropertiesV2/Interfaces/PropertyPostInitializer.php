<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Interfaces;

use Dracodeum\Kit\Managers\PropertiesV2\Property;

/** This interface defines a method to post-initialize a property. */
interface PropertyPostInitializer
{
	//Public methods
	/**
	 * Post-initialize a given property instance.
	 * 
	 * @param \Dracodeum\Kit\Managers\PropertiesV2\Property $property
	 * The property instance to post-initialize.
	 * 
	 * @return void
	 */
	public function postInitializeProperty(Property $property): void;
}
