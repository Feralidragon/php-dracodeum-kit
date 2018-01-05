<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Type\Exceptions;

use Feralygon\Kit\Core\Utilities\Type\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core type utility boolean coercion failed exception class.
 * 
 * This exception is thrown from the type utility whenever the coercion into a boolean has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class BooleanCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Boolean coercion failed with value {{value}}.\n" . 
			"HINT: Only the following types and formats can be coerced into booleans:\n" . 
			" - booleans, as: false for boolean false, and true for boolean true;\n" . 
			" - integers, as: 0 for boolean false, and 1 for boolean true;\n" . 
			" - floats, as: 0.0 for boolean false, and 1.0 for boolean true;\n" . 
			" - strings, as: \"0\", \"f\", \"false\", \"off\" or \"no\" for boolean false, and \"1\", \"t\", \"true\", \"on\" or \"yes\" for boolean true.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
		}
		return null;
	}
}
