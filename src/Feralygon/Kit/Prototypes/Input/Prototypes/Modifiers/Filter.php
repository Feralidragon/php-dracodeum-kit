<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Prototypes\Input\Prototypes\Modifiers;

use Feralygon\Kit\Prototypes\Input\Prototypes\Modifier;

/**
 * @since 1.0.0
 * @see \Feralygon\Kit\Components\Input\Components\Modifiers\Filter
 */
abstract class Filter extends Modifier
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @since 1.0.0
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @return bool
	 * <p>Boolean <code>true</code> if the given value was successfully processed.</p>
	 */
	abstract public function processValue(&$value) : bool;
}
