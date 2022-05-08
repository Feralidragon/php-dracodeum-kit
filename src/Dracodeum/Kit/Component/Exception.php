<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Component;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Component;

/**
 * @property-read \Dracodeum\Kit\Component|string $component
 * The component instance or class.
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsObjectClass(Component::class);
	}
}
