<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Meta\Exception;

class Undefined extends Exception
{
	//Public properties
	public string $name;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return "No property meta entry is defined with the name {{name}} in class {{class}}.";
	}
}
