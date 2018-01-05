<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\Functions\Exceptions;

use Feralygon\Kit\Core\Traits\Functions\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core functions trait function not found exception class.
 * 
 * This exception is thrown from an object using the functions trait whenever a given function is not found.
 * 
 * @since 1.0.0
 * @property-read object $object <p>The object.</p>
 * @property-read string $name <p>The function name.</p>
 */
class FunctionNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Function {{name}} not found in object {{object}}.";
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
				return UType::evaluateString($value);
		}
		return null;
	}
}
