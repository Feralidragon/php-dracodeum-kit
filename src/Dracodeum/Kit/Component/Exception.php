<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Component;

/**
 * @property-read \Dracodeum\Kit\Component|string $component
 * The component instance or class.
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsObjectClass(Component::class);
	}
}
