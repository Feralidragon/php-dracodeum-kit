<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Cloneable;

/**
 * This trait implements the PHP <code>__clone</code> magic method when the cloneable, 
 * the <code>Dracodeum\Kit\Traits\Evaluators</code> and the <code>Dracodeum\Kit\Traits\KeyEvaluators</code> traits 
 * are used.
 * 
 * @see \Dracodeum\Kit\Traits\Evaluators
 * @see \Dracodeum\Kit\Traits\KeyEvaluators
 */
trait KeyEvaluatorsEvaluatorsProcessor
{
	//Public magic methods
	/** Process instance clone. */
	public function __clone(): void
	{
		$this->processEvaluatorsCloning()->processKeyEvaluatorsCloning();
	}
}
