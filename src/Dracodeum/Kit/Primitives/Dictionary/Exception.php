<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Dictionary;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Primitives\Dictionary;

/**
 * @property-read \Dracodeum\Kit\Primitives\Dictionary|string $dictionary [coercive = object or class]
 * <p>The dictionary instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('dictionary')->setAsObjectClass(Dictionary::class);
	}
}
