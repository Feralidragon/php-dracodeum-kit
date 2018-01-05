<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input;
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
 * @property-read \Feralygon\Kit\Core\Components\Input $component <p>The input component instance.</p>
 * @property-read \Feralygon\Kit\Core\Prototypes\Input $prototype <p>The input prototype instance.</p>
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class NotInitialized extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->isset('error_message')
			? "Input {{component}} (with prototype {{prototype}}) could not be initialized due to the following error: {{error_message}}"
			: "Input {{component}} (with prototype {{prototype}}) has not been initialized yet.\n" .
				"HINT: An input must be initialized first by setting a value through the \"setValue\" method.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['component', 'prototype'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'component':
				return is_object($value) && UType::isA($value, Input::class);
			case 'prototype':
				return is_object($value) && UType::isA($value, Input::getPrototypeBaseClass());
			case 'error_message':
				return UType::evaluateString($value, true);
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message') {
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
