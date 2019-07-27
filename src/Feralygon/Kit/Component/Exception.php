<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Component;

/**
 * @property-read \Feralygon\Kit\Component|string $component [coercive = object or class]
 * <p>The component instance or class.</p>
 * @see \Feralygon\Kit\Component
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsObjectClass(Component::class);
	}
}
