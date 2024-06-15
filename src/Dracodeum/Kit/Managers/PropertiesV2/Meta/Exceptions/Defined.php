<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Meta\Exceptions;

use Dracodeum\Kit\Managers\PropertiesV2\Meta\Exception;

class Defined extends Exception
{
	//Public properties
	public string $name;
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function produceText()
	{
		return "A property meta entry is already defined with the name {{name}} in class {{class}}.";
	}
}
