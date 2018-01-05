<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core time utility generate method invalid interval exception class.
 * 
 * This exception is thrown from the time utility generate method whenever a given interval is invalid.
 * 
 * @since 1.0.0
 * @property-read int|float $interval <p>The interval.</p>
 */
class GenerateInvalidInterval extends Generate
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid interval {{interval}}.\n" . 
			"HINT: Only values greater than 0 are allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['interval'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'interval':
				return UType::evaluateNumber($value);
		}
		return null;
	}
}
