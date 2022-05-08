<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Structure;

use Dracodeum\Kit\Exception as KException;
use Dracodeum\Kit\Structure;

/**
 * @property-read \Dracodeum\Kit\Structure|string $structure
 * <p>The structure instance or class.</p>
 */
abstract class Exception extends KException
{
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('structure')->setAsObjectClass(Structure::class);
	}
}
