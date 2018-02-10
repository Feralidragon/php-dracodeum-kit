<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties;

use Feralygon\Kit\Core\Managers\Properties as PropertiesManager;
use Feralygon\Kit\Core\Managers\Properties\Objects\Property;

/**
 * Core lazy properties trait manager class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\LazyProperties
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
