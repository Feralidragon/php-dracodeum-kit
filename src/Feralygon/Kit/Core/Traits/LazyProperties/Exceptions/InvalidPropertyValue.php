<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\LazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\LazyProperties\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core lazy properties trait invalid property value exception class.
 * 
 * This exception is thrown from an object using the lazy properties trait whenever a given value 
 * is invalid for a given property.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 * @property-read mixed $value <p>The property value.</p>
 */
class InvalidPropertyValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid value {{value}} for property {{name}} in object {{object}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['name', 'value']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value);
			case 'value':
				return true;
		}
		return parent::evaluateProperty($name, $value);
	}
}
