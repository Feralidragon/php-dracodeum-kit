<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Objects\Property\Exception;

/**
 * Core properties manager property object method call not allowed exception class.
 * 
 * This exception is thrown from a property object whenever a given method call is not allowed.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The method name.</p>
 * @property-read string|null $hint_message [default = null] <p>The hint message.</p>
 */
class MethodCallNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Method {{name}} call not allowed in property {{property.getName()}} from properties manager " . 
			"with owner {{property.getManager().getOwner()}}.";
		if ($this->isset('hint_message')) {
			$message .= "\n" . 
				"HINT: {{hint_message}}";
		}
		return $message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
		$this->addStringProperty('hint_message', false, false, true);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'hint_message' && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
