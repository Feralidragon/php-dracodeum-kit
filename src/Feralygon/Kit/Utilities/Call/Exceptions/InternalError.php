<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

/**
 * This exception is thrown from the call utility whenever an internal error occurs in a given function or method call.
 * 
 * @since 1.0.0
 */
class InternalError extends NotAllowed
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = $this->isset('object_class')
			? "Internal error occurred in method call {{function_name}} in {{object_class}}."
			: "Internal error occurred in function call {{function_name}}.";
		
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
