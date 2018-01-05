<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier;

/**
 * Core input constraint modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Constraint
 */
abstract class Constraint extends Modifier
{
	//Abstract public methods
	/**
	 * Check a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value <p>The value to check.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given value is valid.</p>
	 */
	abstract public function checkValue($value) : bool;
}
