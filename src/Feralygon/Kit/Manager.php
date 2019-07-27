<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit;

use Feralygon\Kit\Interfaces\Uncloneable as IUncloneable;

/**
 * This class is the base to be extended from when creating a manager.
 * 
 * @since 1.0.0
 */
abstract class Manager implements IUncloneable
{
	//Traits
	use Traits\Uncloneable;
}
