<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Dictionary;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Primitives\Dictionary;

/**
 * @property-read \Dracodeum\Kit\Primitives\Dictionary|string $dictionary
 * <p>The dictionary instance or class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('dictionary')->setAsObjectClass(Dictionary::class);
	}
}
