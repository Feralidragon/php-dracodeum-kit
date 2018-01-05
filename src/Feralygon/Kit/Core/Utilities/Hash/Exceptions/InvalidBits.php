<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Hash\Exceptions;

use Feralygon\Kit\Core\Utilities\Hash\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core hash utility invalid bits exception class.
 * 
 * This exception is thrown from the hash utility whenever a given number of bits is invalid.
 * 
 * @since 1.0.0
 * @property-read int $bits <p>The number of bits.</p>
 */
class InvalidBits extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid bits {{bits}}.\n" . 
			"HINT: Only multiples of 8 and values greater than 0 are allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['bits'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'bits':
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
