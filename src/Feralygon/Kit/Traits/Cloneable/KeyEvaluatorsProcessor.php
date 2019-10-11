<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Cloneable;

/**
 * This trait implements the PHP <code>__clone</code> magic method when the cloneable 
 * and the <code>Feralygon\Kit\Traits\KeyEvaluators</code> traits are used.
 * 
 * @see \Feralygon\Kit\Traits\KeyEvaluators
 */
trait KeyEvaluatorsProcessor
{
	//Public magic methods
	/** Process instance clone. */
	public function __clone()
	{
		$this->processKeyEvaluatorsCloning();
	}
}
