<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

class Uninitialized extends AccessError
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getString(): string
	{
		return "The property {{names}} from {{manager.getOwner()}} is not initialized.";
	}
	
	/** {@inheritdoc} */
	protected function getPluralString(): string
	{
		return "The properties {{names}} from {{manager.getOwner()}} are not initialized.";
	}
}
