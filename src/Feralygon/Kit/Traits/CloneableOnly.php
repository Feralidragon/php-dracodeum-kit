<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Traits;

use Feralygon\Kit\Utilities\Call as UCall;

/**
 * This trait prevents the instances of a class from being cloned through the PHP <code>clone</code> keyword, 
 * allowing only the <code>clone</code> method defined in the <code>Feralygon\Kit\Interfaces\Cloneable</code> interface 
 * to be used instead.
 * 
 * @since 1.0.0
 * @see \Feralygon\Kit\Interfaces\Cloneable
 */
trait CloneableOnly
{
	//Final public magic methods
	/**
	 * Prevent class instance from being cloned through the PHP <code>clone</code> keyword.
	 * 
	 * @since 1.0.0
	 */
	final public function __clone()
	{
		UCall::guard(false, [
			'error_message' => "Instances of this class cannot be cloned through the PHP \"clone\" keyword.",
			'hint_message' => "In order to clone an instance of this class, the \"clone\" method, " . 
				"as defined in the \"Feralygon\\Kit\\Interfaces\\Cloneable\" interface, must be used instead."
		]);
	}
}
