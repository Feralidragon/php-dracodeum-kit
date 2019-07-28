<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions\Guard;

use Feralygon\Kit\Utilities\Call\Exceptions\Guard as Exception;

/**
 * This exception is thrown from the call utility <code>guard</code> method whenever a given function or method call 
 * is not allowed.
 */
class NotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = $this->isset('object_class')
			? "Method call {{function_name}} not allowed in {{object_class}}."
			: "Function call {{function_name}} not allowed.";
		
		//error message
		if ($this->isset('error_message')) {
			$message .= "\nERROR: {{error_message}}";
		}
		
		//hint message
		if ($this->isset('hint_message')) {
			$message .= "\nHINT: {{hint_message}}";
		}
		
		//return
		return $message;
	}
}
