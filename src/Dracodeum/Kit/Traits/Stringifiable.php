<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Interfaces\Stringifiable as IStringifiable;
use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait adds the ability for an object to be cast to a string, 
 * by using the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface.
 * 
 * @see \Dracodeum\Kit\Interfaces\Stringifiable
 */
trait Stringifiable
{
	//Final public magic methods
	/**
	 * Cast this object to a string.
	 * 
	 * This method requires this object to have 
	 * the <code>Dracodeum\Kit\Interfaces\Stringifiable</code> interface implemented.
	 * 
	 * @return string
	 * <p>This object cast to a string.</p>
	 */
	final public function __toString(): string
	{
		UCall::guard($this instanceof IStringifiable, [
			'hint_message' => "This method requires this object to have " . 
				"the \"Dracodeum\\Kit\\Interfaces\\Stringifiable\" interface implemented."
		]);
		return $this->toString();
	}
}
