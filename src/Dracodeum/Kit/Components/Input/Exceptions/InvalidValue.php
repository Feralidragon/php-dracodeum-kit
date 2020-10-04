<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Components\Input\Exceptions;

use Dracodeum\Kit\Components\Input\Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from an input whenever a given value is invalid.
 * 
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read string|null $error_message [coercive] [default = null]
 * <p>The error message.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->error_message !== null
			? "Invalid value {{value}} for input {{component}} (with prototype {{prototype}}), " . 
				"with the following error: {{error_message}}"
			: "Invalid value {{value}} for input {{component}} (with prototype {{prototype}}).";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		if ($placeholder === 'error_message') {
			return UText::formatMessage($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
