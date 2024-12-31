<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Exceptions;

class Missing extends AccessError
{
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function getString(): string
	{
		return "Missing value for required property {{names}} for {{manager.getOwner()}}.";
	}
	
	/** {@inheritdoc} */
	protected function getPluralString(): string
	{
		return "Missing values for required properties {{names}} for {{manager.getOwner()}}.";
	}
}
