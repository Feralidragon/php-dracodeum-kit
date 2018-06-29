<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Traits\Properties;

/**
 * This trait extends the properties trait and implements both the PHP <code>ArrayAccess</code> 
 * and <code>Feralygon\Kit\Interfaces\Arrayable</code> interfaces.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Traits\Properties
 * @see \Feralygon\Kit\Interfaces\Arrayable
 * @see \ArrayAccess
 */
trait ArrayableAccess
{
	//Traits
	use Properties;
	
	
	
	//Implemented final public methods (Feralygon\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(): array
	{
		return $this->getAll();
	}
	
	
	
	//Implemented final public methods (ArrayAccess)
	/** {@inheritdoc} */
	final public function offsetExists($offset): bool
	{
		return $this->has($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetSet($offset, $value): void
	{
		$this->set($offset, $value);
	}
	
	/** {@inheritdoc} */
	final public function offsetUnset($offset): void
	{
		$this->unset($offset);
	}
}
