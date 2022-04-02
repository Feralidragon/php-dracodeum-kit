<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Exceptions;

class CastFailed extends ProcessFailed
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getLabel(): string
	{
		return "Cast";
	}
}
