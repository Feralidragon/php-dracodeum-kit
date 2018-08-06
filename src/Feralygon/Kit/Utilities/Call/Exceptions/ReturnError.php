<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

/**
 * This exception is thrown from the call utility whenever a return error occurs with a given value 
 * from a given executed function in a given function or method call.
 * 
 * @since 1.0.0
 */
class ReturnError extends ReturnNotAllowed
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = '';
		if ($this->isset('exec_function_full_name')) {
			$message = $this->isset('object_class')
				? "Return error occurred with value {{value}} from function {{exec_function_full_name}} " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return error occurred with value {{value}} from function {{exec_function_full_name}} " . 
					"in function call {{function_name}}.";
		} else {
			$message = $this->isset('object_class')
				? "Return error occurred with value {{value}} from anonymous function " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return error occurred with value {{value}} from anonymous function " . 
					"in function call {{function_name}}.";
		}
		
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
