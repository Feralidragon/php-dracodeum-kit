<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions\Halt;

use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as Exception;

/**
 * This exception is thrown from the call utility <code>halt</code> method whenever a given function or method call 
 * is not allowed.
 */
class NotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = $this->object_class !== null
			? "Method call {{function_name}} not allowed in {{object_class}}."
			: "Function call {{function_name}} not allowed.";
		
		//error message
		if ($this->error_message !== null) {
			$message .= "\nERROR: {{error_message}}";
		}
		
		//hint message
		if ($this->hint_message !== null) {
			$message .= "\nHINT: {{hint_message}}";
		}
		
		//return
		return $message;
	}
}
