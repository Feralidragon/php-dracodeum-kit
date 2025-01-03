<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Interfaces;

/** This interface defines a method to cast an object to a key. */
interface Keyable
{
	//Public methods
	/**
	 * Cast this object to a key.
	 * 
	 * @param bool $recursive [default = false]
	 * <p>Cast to a recursive key with all the possible referenced subobjects.</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string
	 * <p>A key cast from this object.</p>
	 */
	public function toKey(bool $recursive = false, ?bool &$safe = null): string;
}
