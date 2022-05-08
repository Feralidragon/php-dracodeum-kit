<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Factory;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Factory;

/**
 * @property-read string $factory [strict = class]
 * <p>The factory class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('factory')->setAsStrictClass(Factory::class);
	}
}
