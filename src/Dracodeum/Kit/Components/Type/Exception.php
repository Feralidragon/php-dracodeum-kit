<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Components\Type as Component;

/**
 * @property-read \Dracodeum\Kit\Components\Type $component
 * The component instance.
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('component')->setAsStrictObject(Component::class);
	}
}
