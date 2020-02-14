<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Provider;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Components\Provider as Component;

/**
 * @property-read \Dracodeum\Kit\Components\Provider $component [strict]
 * <p>The component instance.</p>
 * @property-read \Dracodeum\Kit\Prototypes\Provider $prototype [strict]
 * <p>The prototype instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class);
		$this->addProperty('prototype')->setAsStrictObject(Component::getBasePrototypeClass());
	}
}
