<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Time\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core time utility time coercion failed exception class.
 * 
 * This exception is thrown from the time utility whenever the coercion into a time has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class TimeCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Time coercion failed with value {{value}}.\n" . 
			"HINT: Only the following types and formats can be coerced into times:\n" . 
			" - numbers in seconds, such as: 50700 for \"14:05:00\";\n" . 
			" - strings as supported by the PHP core \"strtotime\" function, such as: \"2:05PM\" for \"14:05:00\".";
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
