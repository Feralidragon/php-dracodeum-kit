<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Properties\Exceptions;

use Feralygon\Kit\Core\Traits\Properties\Exception;

/**
 * Core properties trait properties not initialized exception class.
 * 
 * This exception is thrown from an object using the properties trait whenever properties have not been initialized yet.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 */
class PropertiesNotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Properties have not been initialized yet in object {{object}}.\n" . 
			"HINT: Properties must be initialized first through the \"initializeProperties\" method.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['object'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'object':
				return is_object($value);
		}
		return null;
	}
}
