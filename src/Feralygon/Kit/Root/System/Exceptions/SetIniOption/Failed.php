<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions\SetIniOption;

use Feralygon\Kit\Root\System\Exceptions\SetIniOption as Exception;

/**
 * This exception is thrown from the system <code>setIniOption</code> method whenever it fails.
 * 
 * @since 1.0.0
 */
class Failed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Option {{name}} failed to be set as {{value}}.\n" . 
			"HINT: Only existing options can be set, each with its own set of allowed values.";
	}
}
