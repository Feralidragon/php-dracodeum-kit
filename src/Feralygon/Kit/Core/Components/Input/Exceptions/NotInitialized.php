<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input\Exception;
use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core input component not initialized exception class.
 * 
 * This exception is thrown from an input whenever it has not been initialized yet.
 * 
 * @since 1.0.0
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class NotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Input {{component}} (with prototype {{prototype}}) " . 
				"could not be initialized due to the following error: {{error_message}}"
			: "Input {{component}} (with prototype {{prototype}}) " . 
				"has not been initialized yet.\n" .
				"HINT: An input must be initialized first by setting a value through the \"setValue\" method.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'error_message':
				return UType::evaluateString($value, false, true);
		}
		return parent::evaluateProperty($name, $value);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
