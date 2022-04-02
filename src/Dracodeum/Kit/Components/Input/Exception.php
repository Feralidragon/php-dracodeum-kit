<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Components\Input as Component;

/**
 * @property-read \Dracodeum\Kit\Components\Input $component
 * <p>The component instance.</p>
 * @property-read \Dracodeum\Kit\Prototypes\Input $prototype
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
