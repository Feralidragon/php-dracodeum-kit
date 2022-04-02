<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Memoization;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Managers\Memoization as Manager;

/**
 * @property-read \Dracodeum\Kit\Managers\Memoization $manager
 * <p>The manager instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('manager')->setAsStrictObject(Manager::class);
	}
}
