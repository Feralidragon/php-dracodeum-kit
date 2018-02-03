<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core properties trait cannot set property exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever a given property 
 * with a given name is attempted to be set.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The property name.</p>
 */
class CannotSetProperty extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot set property {{name}} in object {{object}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['name']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value);
		}
		return parent::evaluateProperty($name, $value);
	}
}
