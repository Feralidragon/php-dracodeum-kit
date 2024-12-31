<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

class Undefined extends AccessError
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getString(): string
	{
		return "No property is defined with the name {{names}} in {{manager.getOwner()}}.";
	}
	
	/** {@inheritdoc} */
	protected function getPluralString(): string
	{
		return "No properties are defined with the names {{names}} in {{manager.getOwner()}}.";
	}
}
