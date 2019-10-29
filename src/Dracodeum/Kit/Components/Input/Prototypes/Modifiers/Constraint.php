<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Prototypes\Modifiers;

use Dracodeum\Kit\Components\Input\Prototypes\Modifier;

/** @see \Dracodeum\Kit\Components\Input\Components\Modifiers\Constraint */
abstract class Constraint extends Modifier
{
	//Abstract public methods
	/**
	 * Check a given value.
	 * 
	 * @param mixed $value
	 * <p>The value to check.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value is valid.</p>
	 */
	abstract public function checkValue($value): bool;
}
