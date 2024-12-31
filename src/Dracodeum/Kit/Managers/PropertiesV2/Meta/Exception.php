<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Managers\PropertiesV2\Meta;

use Dracodeum\Kit\ExceptionV2 as KException;
use Dracodeum\Kit\Attributes\Property\Strict;

abstract class Exception extends KException
{
	//Public properties
	#[Strict('class')]
	public string $class;
}
