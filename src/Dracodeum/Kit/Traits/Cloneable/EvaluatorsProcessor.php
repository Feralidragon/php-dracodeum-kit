<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Cloneable;

/**
 * This trait implements the PHP <code>__clone</code> magic method when the cloneable 
 * and the <code>Dracodeum\Kit\Traits\Evaluators</code> traits are used.
 * 
 * @see \Dracodeum\Kit\Traits\Evaluators
 */
trait EvaluatorsProcessor
{
	//Public magic methods
	/** Process instance clone. */
	public function __clone(): void
	{
		$this->processEvaluatorsCloning();
	}
}
