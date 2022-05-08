<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Properties\Property;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Managers\Properties\Property;

/**
 * @property-read \Dracodeum\Kit\Managers\Properties\Property $property
 * <p>The property instance.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('property')->setAsStrictObject(Property::class);
	}
}
