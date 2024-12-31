<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type;

use Dracodeum\Kit\ExceptionV2 as KException;
use Dracodeum\Kit\Components\Type as Component;

abstract class Exception extends KException
{
	//Public properties
	public Component $component;
}
