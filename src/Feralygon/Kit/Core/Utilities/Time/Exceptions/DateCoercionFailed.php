<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Time\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core time utility date coercion failed exception class.
 * 
 * This exception is thrown from the time utility whenever the coercion into a date has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class DateCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Date coercion failed with value {{value}}.\n" . 
			"HINT: Only the following types and formats can be coerced into dates:\n" . 
			" - numbers in seconds since 1970-01-01, such as: 1483228800 for \"2017-01-01\";\n" . 
			" - strings as supported by the PHP core \"strtotime\" function, such as: \"2017-Jan-01\" for \"2017-01-01\".";
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
