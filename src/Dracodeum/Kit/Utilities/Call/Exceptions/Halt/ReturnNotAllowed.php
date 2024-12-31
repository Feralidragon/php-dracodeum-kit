<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions\Halt;

class ReturnNotAllowed extends ReturnError
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = '';
		if ($this->exec_function_full_name !== null) {
			$message = $this->object_class !== null
				? "Return value {{value}} not allowed from function {{exec_function_full_name}} " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return value {{value}} not allowed from function {{exec_function_full_name}} " . 
					"in function call {{function_name}}.";
		} else {
			$message = $this->object_class !== null
				? "Return value {{value}} not allowed from anonymous function " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return value {{value}} not allowed from anonymous function " . 
					"in function call {{function_name}}.";
		}
		
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
