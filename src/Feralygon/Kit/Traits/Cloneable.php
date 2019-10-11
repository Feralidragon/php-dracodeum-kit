<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

/**
 * This trait implements the <code>Feralygon\Kit\Interfaces\Cloneable</code> interface.
 * 
 * @see \Feralygon\Kit\Interfaces\Cloneable
 * @see \Feralygon\Kit\Traits\Cloneable\EvaluatorsProcessor
 * @see \Feralygon\Kit\Traits\Cloneable\KeyEvaluatorsProcessor
 * @see \Feralygon\Kit\Traits\Cloneable\KeyEvaluatorsEvaluatorsProcessor
 * @see \Feralygon\Kit\Traits\Cloneable\MemoizationProcessor
 * @see \Feralygon\Kit\Traits\Cloneable\ReadonlyProcessor
 */
trait Cloneable
{
	//Implemented public methods (Feralygon\Kit\Interfaces\Cloneable)
	/** {@inheritdoc} */
	public function clone(bool $recursive = false): object
	{
		return clone $this;
	}
}
