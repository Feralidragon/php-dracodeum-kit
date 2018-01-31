<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits;

/**
 * Core lazy properties array access trait.
 * 
 * This trait extends the lazy properties trait and implements the PHP core <code>ArrayAccess</code> interface.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Core\Traits\LazyProperties
 */
trait LazyPropertiesArrayAccess
{
	//Traits
	use LazyProperties;
	
	
	
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
