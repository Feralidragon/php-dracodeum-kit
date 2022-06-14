<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

class Inaccessible extends ScopedAccessError
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getAdjective(): string
	{
		return 'accessible';
	}
}
