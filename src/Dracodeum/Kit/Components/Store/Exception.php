<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Store;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Components\Store as Component;

/**
 * @property-read \Dracodeum\Kit\Components\Store $component [strict]
 * <p>The component instance.</p>
 * @property-read \Dracodeum\Kit\Prototypes\Store $prototype [strict]
 * <p>The prototype instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class);
		$this->addProperty('prototype')->setAsStrictObject(Component::getPrototypeBaseClass());
	}
}
