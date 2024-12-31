<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Properties;

use Dracodeum\Kit\Managers\Properties as PropertiesManager;
use Dracodeum\Kit\Managers\Properties\Property as ManagerProperty;

final class Manager extends PropertiesManager
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function createProperty(string $name): ManagerProperty
	{
		return new Property($this, $name);
	}
}
