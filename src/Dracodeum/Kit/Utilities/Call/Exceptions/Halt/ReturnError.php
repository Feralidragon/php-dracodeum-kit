<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Call\Exceptions\Halt;

use Dracodeum\Kit\Utilities\Call\Exceptions\Halt as Exception;

/**
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string|null $exec_function_full_name [default = null]
 * <p>The executed function full name.</p>
 */
class ReturnError extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = '';
		if ($this->exec_function_full_name !== null) {
			$message = $this->object_class !== null
				? "Return error occurred with value {{value}} from function {{exec_function_full_name}} " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return error occurred with value {{value}} from function {{exec_function_full_name}} " . 
					"in function call {{function_name}}.";
		} else {
			$message = $this->object_class !== null
				? "Return error occurred with value {{value}} from anonymous function " . 
					"in method call {{function_name}} in {{object_class}}."
				: "Return error occurred with value {{value}} from anonymous function " . 
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
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('exec_function_full_name')->setAsString(false, true)->setDefaultValue(null);
	}
}
