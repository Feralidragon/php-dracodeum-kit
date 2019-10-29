<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structure;

use Dracodeum\Kit\Exception as KitException;
use Dracodeum\Kit\Structure;

/**
 * @property-read \Dracodeum\Kit\Structure|string $structure [coercive = object or class]
 * <p>The structure instance or class.</p>
 */
abstract class Exception extends KitException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('structure')->setAsObjectClass(Structure::class);
	}
}
