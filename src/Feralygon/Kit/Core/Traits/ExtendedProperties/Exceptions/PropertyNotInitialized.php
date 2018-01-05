<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedProperties\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedProperties\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core extended properties trait property not initialized exception class.
 * 
 * This exception is thrown from an object using the extended properties trait whenever a given property has not been initialized yet.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @property-read string $name <p>The property name.</p>
 */
class PropertyNotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Property {{name}} has not been initialized yet in object {{object}}.\n" . 
			"HINT: Properties must be initialized first with a value or default value.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['object', 'name'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'object':
				return is_object($value);
			case 'name':
				return UType::evaluateString($value) && UText::isIdentifier($value);
		}
		return null;
	}
}
