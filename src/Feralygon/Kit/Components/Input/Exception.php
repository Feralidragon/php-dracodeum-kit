<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Components\Input as Component;

/**
 * @property-read \Feralygon\Kit\Components\Input $component [strict]
 * <p>The component instance.</p>
 * @property-read \Feralygon\Kit\Prototypes\Input $prototype [strict]
 * <p>The prototype instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class);
		$this->addProperty('prototype')->setAsStrictObject(Component::getBasePrototypeClass());
	}
}
