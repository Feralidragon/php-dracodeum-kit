<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Time\Exception;

/**
 * Core time utility invalid timestamp exception class.
 * 
 * This exception is thrown from the time utility whenever a given timestamp is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $timestamp <p>The timestamp.</p>
 */
class InvalidTimestamp extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid timestamp {{timestamp}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['timestamp'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'timestamp':
				return true;
		}
		return null;
	}
}
