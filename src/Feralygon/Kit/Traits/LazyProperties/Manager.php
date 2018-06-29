<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\LazyProperties;

use Feralygon\Kit\Managers\Properties as PropertiesManager;

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\LazyProperties
 */
final class Manager extends PropertiesManager
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function createProperty(string $name): PropertiesManager\Property
	{
		return new Property($this, $name);
	}
}
