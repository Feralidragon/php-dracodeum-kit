<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property;
use Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core extended properties trait property object invalid value exception class.
 * 
 * This exception is thrown from an extended properties trait property object whenever a given value is invalid.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Traits\ExtendedProperties\Objects\Property $property <p>The property instance.</p>
 * @property-read mixed $value <p>The value.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{property}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['property', 'value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'property':
				return is_object($value) && UType::isA($value, Property::class);
			case 'value':
				return true;
		}
		return null;
	}
}
