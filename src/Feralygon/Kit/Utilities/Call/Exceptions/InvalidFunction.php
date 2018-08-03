<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

use Feralygon\Kit\Utilities\Call\Exception;

/**
 * This exception is thrown from the call utility whenever a given function is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $function
 * <p>The function.</p>
 */
class InvalidFunction extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid function {{function}}.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('function');
	}
}
