<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Managers\Properties\Exceptions;

use Feralygon\Kit\Core\Managers\Properties\Exception;

/**
 * Core properties manager method call not allowed exception class.
 * 
 * This exception is thrown from a properties manager whenever a given method call is not allowed.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The method name.</p>
 * @property-read bool|null $lazy [default = null] <p>The lazy-loading state.</p>
 * @property-read string|null $hint_message [default = null] <p>The hint message.</p>
 */
class MethodCallNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		//message
		$message = "Method {{name}} call not allowed in properties manager with owner {{manager.getOwner()}}.";
		
		//lazy
		if ($this->isset('lazy')) {
			$message = $this->is('lazy')
				? "Method {{name}} call not allowed with lazy-loading enabled in properties manager " . 
					"with owner {{manager.getOwner()}}."
				: "Method {{name}} call not allowed with lazy-loading disabled in properties manager " . 
					"with owner {{manager.getOwner()}}.";
		}
		
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
	protected function loadProperties() : void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addStringProperty('name', true);
		$this->addBooleanProperty('lazy', false, true);
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
