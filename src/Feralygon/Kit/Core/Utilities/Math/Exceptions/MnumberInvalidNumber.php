<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Math\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core math utility <code>mnumber</code> method invalid number exception class.
 * 
 * This exception is thrown from the math utility <code>mnumber</code> method whenever a given number is invalid.
 * 
 * @since 1.0.0
 * @property-read string $number <p>The number.</p>
 */
class MnumberInvalidNumber extends Mnumber
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid number {{number}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['number'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'number':
				return UType::evaluateString($value);
		}
		return null;
	}
}
