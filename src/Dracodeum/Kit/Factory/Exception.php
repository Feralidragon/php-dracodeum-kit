<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factory;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Factory;

/**
 * @property-read string $factory [strict = class]
 * <p>The factory class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('factory')->setAsStrictClass(Factory::class);
	}
}
