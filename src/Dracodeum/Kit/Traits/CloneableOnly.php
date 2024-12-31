<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Traits;

use Dracodeum\Kit\Utilities\Call as UCall;

/**
 * This trait prevents the instances of a class from being cloned through the PHP <code>clone</code> keyword, 
 * allowing only the <code>clone</code> method defined in the <code>Dracodeum\Kit\Interfaces\Cloneable</code> interface 
 * to be used instead.
 * 
 * @see \Dracodeum\Kit\Interfaces\Cloneable
 */
trait CloneableOnly
{
	//Final public magic methods
	/** Prevent class instance from being cloned through the PHP <code>clone</code> keyword. */
	final public function __clone(): void
	{
		UCall::halt([
			'error_message' => "Instances of this class cannot be cloned through the PHP \"clone\" keyword.",
			'hint_message' => "In order to clone an instance of this class, the \"clone\" method, " . 
				"as defined in the \"Dracodeum\\Kit\\Interfaces\\Cloneable\" interface, must be used instead."
		]);
	}
}
