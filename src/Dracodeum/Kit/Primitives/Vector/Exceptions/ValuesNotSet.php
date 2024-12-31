<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector\Exceptions;

use Dracodeum\Kit\Primitives\Vector\Exception;

class ValuesNotSet extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "No values set in vector {{vector}}.";
	}
}
