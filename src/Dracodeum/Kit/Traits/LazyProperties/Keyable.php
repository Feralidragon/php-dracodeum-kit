<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\LazyProperties;

/**
 * This trait implements the <code>Dracodeum\Kit\Interfaces\Keyable</code> interface 
 * when the lazy properties trait is used.
 * 
 * @see \Dracodeum\Kit\Interfaces\Keyable
 */
trait Keyable
{
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Keyable)
	/** {@inheritdoc} */
	final public function toKey(bool $recursive = false, ?bool &$safe = null): string
	{
		return $this->getPropertiesManager()->toKey($recursive, $safe);
	}
}
