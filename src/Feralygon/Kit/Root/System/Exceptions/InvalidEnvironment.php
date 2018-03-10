<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Root\System\Exceptions;

use Feralygon\Kit\Root\System\Exception;

/**
 * This exception is thrown from the system whenever a given environment is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $environment <p>The environment.</p>
 */
class InvalidEnvironment extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid environment {{environment}}.\n" . 
			"HINT: Only an environment instance, class or name is allowed.";
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		$this->addProperty('environment')->setAsRequired();
	}
}
