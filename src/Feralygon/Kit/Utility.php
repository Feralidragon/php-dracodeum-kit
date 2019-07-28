<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\NonInstantiable as INonInstantiable;

/**
 * This class is the base to be extended from when creating an utility.
 * 
 * All methods of this kind of class must be <code>static</code>.
 * 
 * @see https://en.wikipedia.org/wiki/Utility_class
 */
abstract class Utility implements INonInstantiable
{
	//Traits
	use Traits\NonInstantiable;
}
