<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Exceptions;

use Dracodeum\Kit\ExceptionV2 as Exception;

abstract class Argument extends Exception
{
	//Public properties
	public string $name;
}
