<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Byte\Exceptions;

use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core byte utility mvalue method invalid value exception class.
 * 
 * This exception is thrown from the byte utility mvalue method whenever a given value is invalid.
 * 
 * @since 1.0.0
 * @property-read string $value <p>The value.</p>
 */
class MvalueInvalidValue extends Mvalue
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return UType::evaluateString($value);
		}
		return null;
	}
}
