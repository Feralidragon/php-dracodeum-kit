<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit;

use Dracodeum\Kit\Interfaces\Uninstantiable as IUninstantiable;

/**
 * This class is the base to be extended from when creating a utility.
 * 
 * All methods of this kind of class must be <code>static</code>.
 * 
 * @see https://en.wikipedia.org/wiki/Utility_class
 */
abstract class Utility implements IUninstantiable
{
	//Traits
	use Traits\Uninstantiable;
}
