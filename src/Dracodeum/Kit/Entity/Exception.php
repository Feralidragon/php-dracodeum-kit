<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Entity;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Entity;

/**
 * @property-read \Dracodeum\Kit\Entity|string $entity
 * <p>The entity instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('entity')->setAsObjectClass(Entity::class);
	}
}
