<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization\Store;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Memoization\Store;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Memoization\Store $store
 * <p>The store instance.</p>
 * @see \Feralygon\Kit\Managers\Memoization\Store
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('store')->setAsStrictObject(Store::class);
	}
}
