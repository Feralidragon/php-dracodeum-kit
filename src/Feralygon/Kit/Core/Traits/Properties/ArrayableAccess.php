<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties;

use Feralygon\Kit\Core\Traits\Properties;

/**
 * Core properties arrayable access trait.
 * 
 * This trait extends the properties trait and implements both the PHP core <code>ArrayAccess</code> 
 * and <code>\Feralygon\Kit\Core\Interfaces\Arrayable</code> interfaces.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\Properties
 * @see \Feralygon\Kit\Core\Interfaces\Arrayable
 * @see \ArrayAccess
 */
trait ArrayableAccess
{
	//Traits
	use Properties;
	
	
	
	//Implemented final public methods (core arrayable interface)
	/** {@inheritdoc} */
	final public function toArray() : array
	{
		return $this->getProperties();
	}
	
	
	
	//Implemented final public methods (PHP array access interface)
	/** {@inheritdoc} */
	final public function offsetExists($offset) : bool
	{
		return $this->has($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetGet($offset)
	{
		return $this->get($offset);
	}
	
	/** {@inheritdoc} */
	final public function offsetSet($offset, $value) : void
	{
		$this->set($offset, $value);
	}
	
	/** {@inheritdoc} */
	final public function offsetUnset($offset) : void
	{
		$this->unset($offset);
	}
}
