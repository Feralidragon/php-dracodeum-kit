<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers;

use Dracodeum\Kit\Components\Input\Prototypes\Modifier;

/** @see \Dracodeum\Kit\Components\Input\Components\Modifiers\Filter */
abstract class Filter extends Modifier
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully processed.</p>
	 */
	abstract public function processValue(&$value): bool;
}
