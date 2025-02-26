<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudioluis8@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Text\Exceptions;

use Dracodeum\Kit\Primitives\Text\Exception;
use Dracodeum\Kit\Interfaces\Throwables\CoercivePhp8 as ICoercive;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read mixed $value
 * The value.
 * 
 * @property-read string|null $error_message [default = null]  
 * The error message.
 */
class CoercionFailed extends Exception implements ICoercive
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Coercion failed with value {{value}} in class {{primitive}}" . 
			($this->error_message !== null ? ", with the following error: {{error_message}}" : ".");
	}
	
	
	
	//Implemented public methods (Dracodeum\Kit\Interfaces\Throwables\CoercivePhp8)
	/** {@inheritdoc} */
	public function getValue(): mixed
	{
		return $this->value;
	}
	
	/** {@inheritdoc} */
	public function getErrorMessage(): ?string
	{
		return $this->error_message;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('value');
		$this->addProperty('error_message')->setAsString(true, true)->setDefaultValue(null);
	}
	
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value): string
	{
		return $placeholder === 'error_message' && $value !== null
			? UText::formatMessage($value, true)
			: parent::getPlaceholderValueString($placeholder, $value);
	}
}
