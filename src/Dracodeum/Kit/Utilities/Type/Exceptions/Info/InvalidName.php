<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Utilities\Type\Exceptions\Info;

use Dracodeum\Kit\Utilities\Type\Exceptions\Info as Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read string $name
 * The name.
 * 
 * @property-read string|null $error_message [default = null]  
 * The error message.
 */
class InvalidName extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid name {{name}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
	
	
	
	//Implemented protected methods (Dracodeum\Kit\Exception\Traits\PropertiesLoader)
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		$this->addProperty('name')->setAsString();
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return $placeholder === 'error_message'
			? UText::formatMessage($value, true)
			: parent::getPlaceholderValueString($placeholder, $value);
	}
}
