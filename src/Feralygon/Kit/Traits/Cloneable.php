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
