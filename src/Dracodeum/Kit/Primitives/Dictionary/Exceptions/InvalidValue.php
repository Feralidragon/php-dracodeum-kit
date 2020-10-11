<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Dictionary\Exceptions;

use Dracodeum\Kit\Primitives\Dictionary\Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from a dictionary whenever a given value is invalid.
 * 
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read bool $has_key [coercive] [default = false]
 * <p>Indicate that a key has been given.</p>
 * @property-read mixed $key [default = null]
 * <p>The key.</p>
 * @property-read string|null $error_message [coercive] [default = null]
 * <p>The error message.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid value {{value}} for dictionary {{dictionary}}" . ($this->has_key ? " at key {{key}}" : "") . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('has_key')->setAsBoolean()->setDefaultValue(false);
		$this->addProperty('key')->setDefaultValue(null);
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
