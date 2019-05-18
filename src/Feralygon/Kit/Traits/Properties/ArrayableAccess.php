<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits\Properties;

use Feralygon\Kit\Traits\Properties;
use Feralygon\Kit\Interfaces\Arrayable as IArrayable;

/**
 * This trait extends the properties trait and implements both the PHP <code>ArrayAccess</code> 
 * and <code>Feralygon\Kit\Interfaces\Arrayable</code> interfaces.
 * 
 * @since 1.0.0
 * @see https://php.net/manual/en/class.arrayaccess.php
 * @see \Feralygon\Kit\Traits\Properties
 * @see \Feralygon\Kit\Interfaces\Arrayable
 */
trait ArrayableAccess
{
	//Traits
	use Properties;
	
	
	
	//Implemented final public methods (Feralygon\Kit\Interfaces\Arrayable)
	/** {@inheritdoc} */
	final public function toArray(bool $recursive = false): array
	{
		$array = $this->getAll();
		if ($recursive) {
			foreach ($array as &$value) {
				if (is_object($value) && $value instanceof IArrayable) {
					$value = $value->toArray($recursive);
				}
			}
			unset($value);
		}
		return $array;
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
