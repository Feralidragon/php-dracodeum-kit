<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifiers;

use Feralygon\Kit\Core\Prototypes\Input\Prototypes\Modifier;

/**
 * Core input filter modifier prototype class.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Components\Input\Components\Modifiers\Filter
 */
abstract class Filter extends Modifier
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference] <p>The value to process.</p>
	 * @return bool <p>Boolean <samp>true</samp> if the given value was processed successfully.</p>
	 */
	abstract public function processValue(&$value) : bool;
}
