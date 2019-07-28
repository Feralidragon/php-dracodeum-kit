<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions\Guard;

use Feralygon\Kit\Utilities\Call\Exceptions\Guard as Exception;

/**
 * This exception is thrown from the call utility <code>guardParameter</code> method whenever a given parameter 
 * from a given function or method call is not allowed.
 * 
 * @property-read string $name [coercive]
 * <p>The name.</p>
 * @property-read mixed $value
 * <p>The value.</p>
 */
class ParameterNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		//message
		$message = $this->isset('object_class')
			? "Parameter {{name}} not allowed to be given as {{value}} " . 
				"in method call {{function_name}} in {{object_class}}."
			: "Parameter {{name}} not allowed to be given as {{value}} " . 
				"in function call {{function_name}}.";
		
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
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('name')->setAsString();
		$this->addProperty('value');
	}
}
