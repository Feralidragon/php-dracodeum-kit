<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Entity;

/**
 * @property-read \Dracodeum\Kit\Entity|string $entity
 * <p>The entity instance or class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('entity')->setAsObjectClass(Entity::class);
	}
}
