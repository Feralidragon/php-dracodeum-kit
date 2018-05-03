<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Call\Exceptions;

use Feralygon\Kit\Utilities\Call\Exception;

/**
 * This exception is thrown from the call utility whenever a given function or method call is not allowed.
 * 
 * @since 1.0.0
 * @property-read string $function_name
 * <p>The function or method name.</p>
 * @property-read object|string|null $object_class [default = null]
 * <p>The object or class.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 * @property-read string|null $hint_message [default = null]
 * <p>The hint message.</p>
 */
class NotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//message
		$message = $this->isset('object_class')
			? "Method {{function_name}} call not allowed in {{object_class}}."
			: "Function {{function_name}} call not allowed.";
		
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
	
	
	
	//Implemented protected methods (Feralygon\Kit\Exception\Traits\Properties)
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('function_name')->setAsString()->setAsRequired();
		$this->addProperty('object_class')->setAsObjectClass(null, true)->setDefaultValue(null);
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
		$this->addProperty('hint_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if (($placeholder === 'error_message' || $placeholder === 'hint_message') && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
