<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Objects\Property\Exception;

/**
 * Core extended lazy properties trait property object invalid value exception class.
 * 
 * This exception is thrown from an extended lazy properties trait property object whenever a given value is invalid.
 * 
 * @since 1.0.0
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
