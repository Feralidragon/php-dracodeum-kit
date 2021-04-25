<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Prototypes;

use Dracodeum\Kit\Prototype;
use Dracodeum\Kit\Primitives\Error;

/**
 * @see \Dracodeum\Kit\Components\Type
 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\MutatorProducer
 * @see \Dracodeum\Kit\Prototypes\Type\Interfaces\Textifier
 */
abstract class Type extends Prototype
{
	//Abstract public methods
	/**
	 * Process a given value.
	 * 
	 * @param mixed $value
	 * The value to process.
	 * 
	 * @param enum<\Dracodeum\Kit\Components\Type\Enumerations\Context> $context
	 * The context to process for.
	 * 
	 * @return \Dracodeum\Kit\Primitives\Error|null
	 * An error instance if the given value failed to be processed, or `null` if otherwise.
	 */
	abstract public function process(mixed &$value, $context): ?Error;
}
