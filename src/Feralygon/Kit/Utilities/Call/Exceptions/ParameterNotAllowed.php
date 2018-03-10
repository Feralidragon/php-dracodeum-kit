<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

/**
 * This exception is thrown from the call utility whenever a given parameter from a given function or method call 
 * is not allowed.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The parameter name.</p>
 * @property-read mixed $value <p>The parameter value.</p>
 */
class ParameterNotAllowed extends NotAllowed
{
	//Overridden public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//message
		$message = $this->isset('object_class')
			? "Parameter {{name}} not allowed to be given as {{value}} " . 
				"in method {{function_name}} call in {{object_class}}."
			: "Parameter {{name}} not allowed to be given as {{value}} " . 
				"in function {{function_name}} call.";
		
		//hint message
		if ($this->isset('hint_message')) {
			$message .= "\n" . 
				"HINT: {{hint_message}}";
		}
		
		//return
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function buildProperties() : void
	{
		//parent
		parent::buildProperties();
		
		//properties
		$this->addProperty('name')->setAsString()->setAsRequired();
		$this->addProperty('value')->setAsRequired();
	}
}
