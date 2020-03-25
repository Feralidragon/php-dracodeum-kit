<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
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
	 * <p>Cast to a recursive key with all the possible referenced subobjects (if applicable).</p>
	 * @param bool|null $safe [reference output] [default = null]
	 * <p>The safety indicator which, if set to boolean <code>true</code>, 
	 * indicates that the returning key may be used for longer term purposes, such as internal cache keys.</p>
	 * @return string
	 * <p>This object cast to a key.</p>
	 */
	public function toKey(bool $recursive = false, ?bool &$safe = null): string;
}
