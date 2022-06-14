<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2;

use Dracodeum\Kit\ExceptionV2 as KException;
use Dracodeum\Kit\Managers\PropertiesV2 as Manager;

abstract class Exception extends KException
{
	//Public properties
	public Manager $manager;
}
