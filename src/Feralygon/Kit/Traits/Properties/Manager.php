<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Managers\Properties as PropertiesManager;
use Feralygon\Kit\Managers\Properties\Objects\Property;

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\Properties
 */
final class Manager extends PropertiesManager
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function createProperty(string $name) : Property
	{
		return new Objects\Property($this, $name);
	}
}
