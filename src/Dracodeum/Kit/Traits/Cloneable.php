<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

/**
 * This trait implements the <code>Dracodeum\Kit\Interfaces\Cloneable</code> interface.
 * 
 * @see \Dracodeum\Kit\Interfaces\Cloneable
 * @see \Dracodeum\Kit\Traits\Cloneable\EvaluatorsProcessor
 * @see \Dracodeum\Kit\Traits\Cloneable\KeyEvaluatorsProcessor
 * @see \Dracodeum\Kit\Traits\Cloneable\KeyEvaluatorsEvaluatorsProcessor
 * @see \Dracodeum\Kit\Traits\Cloneable\MemoizationProcessor
 * @see \Dracodeum\Kit\Traits\Cloneable\ReadonlyProcessor
 */
trait Cloneable
{
	//Implemented public methods (Dracodeum\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	public function clone(): object
	{
		return clone $this;
	}
}
