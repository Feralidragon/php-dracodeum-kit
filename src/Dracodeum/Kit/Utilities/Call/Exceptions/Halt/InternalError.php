<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions\Halt;

use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as Exception;

class InternalError extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = $this->object_class !== null
			? "Internal error occurred in method call {{function_name}} in {{object_class}}."
			: "Internal error occurred in function call {{function_name}}.";
		
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
