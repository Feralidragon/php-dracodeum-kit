<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Root\System\Exceptions\SetIniOption;

use Dracodeum\Kit\Root\System\Exceptions\SetIniOption as Exception;

class Failed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Option {{name}} failed to be set as {{value}}.\n" . 
			"HINT: Only existing options can be set, each with its own set of allowed values.";
	}
}
