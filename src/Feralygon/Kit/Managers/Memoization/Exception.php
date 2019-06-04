<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Managers\Memoization;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Managers\Memoization as Manager;

/**
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Managers\Memoization $manager [strict]
 * <p>The manager instance.</p>
 * @see \Feralygon\Kit\Managers\Memoization
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('manager')->setAsStrictObject(Manager::class);
	}
}
