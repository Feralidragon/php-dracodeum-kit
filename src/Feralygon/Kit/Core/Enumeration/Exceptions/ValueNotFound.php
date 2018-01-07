<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration\Exception;

/**
 * Core enumeration value not found exception class.
 * 
 * This exception is thrown from an enumeration whenever a given value is not found.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class ValueNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Value {{value}} not found in enumeration {{enumeration}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['value']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
		}
		return parent::evaluateProperty($name, $value);
	}
}
