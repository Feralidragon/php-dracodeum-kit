<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Time\Exceptions;

use Feralygon\Kit\Core\Utilities\Time\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core time utility timestamp coercion failed exception class.
 * 
 * This exception is thrown from the time utility whenever the coercion into a timestamp has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class TimestampCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Timestamp coercion failed with value {{value}}.\n" . 
			"HINT: Only the following types and formats can be coerced into timestamps:\n" . 
			" - numbers in seconds since 1970-01-01 00:00:00 UTC, such as: 1483268400 for \"2017-01-01 12:00:00\";\n" . 
			" - strings as supported by the PHP core \"strtotime\" function, such as: \"2017-Jan-01 12:00:00\" for \"2017-01-01 12:00:00\".";
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
