<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Type\Prototypes;

use Dracodeum\Kit\Prototype;

/**
 * @see \Dracodeum\Kit\Components\Type\Components\Mutator
 * @see \Dracodeum\Kit\Components\Type\Prototypes\Mutator\Interfaces\ExplanationProducer
 */
abstract class Mutator extends Prototype
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value [reference]
	 * <p>The value to process.</p>
	 * @return \Dracodeum\Kit\Primitives\Error|bool|null|void
	 * <p>An error instance or boolean <code>false</code> if the given value failed to be processed, 
	 * or <code>null</code>, boolean <code>true</code> or <code>void</code> if otherwise.</p>
	 */
	abstract public function process(mixed &$value);
}
