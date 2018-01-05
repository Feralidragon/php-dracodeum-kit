<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core;

/**
 * Core utility class.
 * 
 * This class is the base to be extended from when creating an utility.<br>
 * All methods of this kind of class must be <code>static</code>.
 * 
 * @since 1.0.0
 * @see https://en.wikipedia.org/wiki/Utility_class
 */
abstract class Utility
{
	//Traits
	use Traits\NonInstantiable;
}
