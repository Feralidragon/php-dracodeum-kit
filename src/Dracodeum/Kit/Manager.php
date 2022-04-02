<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\Uncloneable as IUncloneable;

/** This class is the base to be extended from when creating a manager. */
abstract class Manager implements IUncloneable
{
	//Traits
	use Traits\Uncloneable;
}
