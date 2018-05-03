<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Component;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Component;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Component|string $component
 * <p>The component instance or class.</p>
 * @see \Feralygon\Kit\Component
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('component')->setAsObjectClass(Component::class)->setAsRequired();
	}
}
