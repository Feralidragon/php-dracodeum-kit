<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;

/**
 * Core functions trait functions not initialized exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever functions have not been initialized yet.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 */
class FunctionsNotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Functions have not been initialized yet in object {{object}}.\n" . 
			"HINT: Functions must be initialized first through the \"initializeFunctions\" method.";
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
