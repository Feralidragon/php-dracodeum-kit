<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Type\Exceptions;

use Feralygon\Kit\Core\Utilities\Type\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core type utility number coercion failed exception class.
 * 
 * This exception is thrown from the type utility whenever the coercion into a number has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class NumberCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Number coercion failed with value {{value}}.\n" . 
			"HINT: Only the following types and formats can be coerced into numbers:\n" . 
			" - integers, such as: 123000 for 123000;\n" . 
			" - floats, such as: 123000.45 for 123000.45;\n" . 
			" - numeric strings, such as: \"123000.45\" or \"123000,45\" for 123000.45;\n" . 
			" - numeric strings in exponential notation, such as: \"123e3\" or \"123E3\" for 123000;\n" . 
			" - numeric strings in octal notation, such as: \"0360170\" for 123000;\n" . 
			" - numeric strings in hexadecimal notation, such as: \"0x1e078\" or \"0x1E078\" for 123000;\n" . 
			" - human-readable numeric strings, such as: \"123k\" or \"123 thousand\" for 123000;\n" . 
			" - human-readable numeric strings in bytes, such as: \"123kB\" or \"123 kilobytes\" for 123000.";
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
