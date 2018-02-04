<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Math\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core math utility <code>wrandom</code> method invalid value weight exception class.
 * 
 * This exception is thrown from the math utility <code>wrandom</code> method whenever a given weight is invalid for a given value.
 * 
 * @since 1.0.0
 * @property-read int|string $value <p>The value.</p>
 * @property-read mixed $weight <p>The weight.</p>
 */
class WrandomInvalidValueWeight extends Wrandom
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid weight {{weight}} for value {{value}}.\n" . 
			"HINT: Only a weight greater than or equal to 0 is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value', 'weight'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return UType::evaluateInteger($value) || UType::evaluateString($value);
			case 'weight':
				return true;
		}
		return null;
	}
}
