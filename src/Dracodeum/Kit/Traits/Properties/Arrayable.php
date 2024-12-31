<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits\Properties;

/**
 * This trait implements the <code>Dracodeum\Kit\Interfaces\Arrayable</code> interface 
 * when the properties trait is used.
 * 
 * @see \Dracodeum\Kit\Interfaces\Arrayable
 */
trait Arrayable
{
	//Implemented final public methods (Dracodeum\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(): array
	{
		return $this->getAll();
	}
}
