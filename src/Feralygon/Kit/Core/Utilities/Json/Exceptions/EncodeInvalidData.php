<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Json\Exceptions;

/**
 * Core JSON utility <code>encode</code> method invalid data exception class.
 * 
 * This exception is thrown from the JSON utility <code>encode</code> method whenever given data is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $data <p>The data.</p>
 * @property-read int|null $error_code [default = null] <p>The error code.</p>
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class EncodeInvalidData extends Encode
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid data {{data}}.";
		if ($this->isset('error_message')) {
			$message .= "\nERROR: {{error_message}}";
		}
		return $message;
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addMixedProperty('data', true);
		$this->addIntegerProperty('error_code', false, true);
		$this->addStringProperty('error_message', false, false, true);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
