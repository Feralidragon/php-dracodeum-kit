<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Components\Input\Components\Modifier;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Components\Input\Components\Modifier as Component;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Components\Input\Components\Modifier $component
 * <p>The component instance.</p>
 * @property-read \Feralygon\Kit\Components\Input\Prototypes\Modifier $prototype
 * <p>The prototype instance.</p>
 * @see \Feralygon\Kit\Components\Input\Components\Modifier
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class)->setAsRequired();
		$this->addProperty('prototype')->setAsStrictObject(Component::getBasePrototypeClass())->setAsRequired();
	}
}
