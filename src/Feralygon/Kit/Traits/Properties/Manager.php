<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Managers\Properties as PropertiesManager;

final class Manager extends PropertiesManager
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function createProperty(string $name): PropertiesManager\Property
	{
		return new Property($this, $name);
	}
}
