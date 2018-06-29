<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Time\Exceptions;

use Feralygon\Kit\Utilities\Time\Exception;

/**
 * This exception is thrown from the time utility whenever a given timestamp is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $timestamp
 * <p>The timestamp.</p>
 */
class InvalidTimestamp extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid timestamp {{timestamp}}.\n" . 
			"HINT: Only one of the following is allowed:\n" . 
			" - a string as supported by the PHP \"strtotime\" function;\n" . 
			" - an integer or float as the number of seconds since 1970-01-01 00:00:00 UTC;\n" . 
			" - an object implementing the \"DateTimeInterface\" interface.";
	}
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('timestamp');
	}
}
