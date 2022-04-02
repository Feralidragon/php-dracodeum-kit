<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Interfaces\Stringable as IStringable;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait adds the ability for an object to be cast to a string, 
 * by using the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface.
 * 
 * @see \Dracodeum\Kit\Interfaces\Stringable
 */
trait Stringable
{
	//Final public magic methods
	/**
	 * Cast this object to a string.
	 * 
	 * This method requires this object to have 
	 * the <code>Dracodeum\Kit\Interfaces\Stringable</code> interface implemented.
	 * 
	 * @return string
	 * <p>This object cast to a string.</p>
	 */
	final public function __toString(): string
	{
		UCall::guard($this instanceof IStringable, [
			'hint_message' => "This method requires this object to have " . 
				"the \"Dracodeum\\Kit\\Interfaces\\Stringable\" interface implemented."
		]);
		return $this->toString();
	}
}
