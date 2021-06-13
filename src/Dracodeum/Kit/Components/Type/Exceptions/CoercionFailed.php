<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Exceptions;

class CoercionFailed extends ProcessFailed
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getLabel(): string
	{
		return "Coercion";
	}
}
