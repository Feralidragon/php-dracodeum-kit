<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\Memoization\Store;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Managers\Memoization\Store;

/**
 * @property-read \Dracodeum\Kit\Managers\Memoization\Store $store
 * <p>The store instance.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('store')->setAsStrictObject(Store::class);
	}
}
