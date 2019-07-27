<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Factory;

use Feralygon\Kit\Exception as KitException;
use Feralygon\Kit\Factory;

/**
 * @property-read string $factory [strict = class]
 * <p>The factory class.</p>
 * @see \Feralygon\Kit\Factory
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('factory')->setAsStrictClass(Factory::class);
	}
}
