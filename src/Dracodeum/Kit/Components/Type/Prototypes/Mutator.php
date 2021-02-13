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
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|bool|null|void
	 * An error instance or boolean `false` if the given value failed to be processed, 
	 * or `null`, boolean `true` or `void` if otherwise.
	 */
	abstract public function process(mixed &$value);
}
