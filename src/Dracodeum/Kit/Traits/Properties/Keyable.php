<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Properties;

/**
 * This trait implements the <code>Dracodeum\Kit\Interfaces\Keyable</code> interface when the properties trait is used.
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
