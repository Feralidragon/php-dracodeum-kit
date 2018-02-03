<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Exceptions;

use Feralygon\Kit\Core\Traits\ExtendedLazyProperties\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core extended lazy properties trait property not initialized exception class.
 * 
 * This exception is thrown from an object using the extended lazy properties trait whenever a given property 
 * has not been initialized yet.
 * 
 * @since 1.0.0
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
