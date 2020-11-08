<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector\Exceptions;

use Dracodeum\Kit\Primitives\Vector\Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read int|null $index [default = null]
 * <p>The index.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return "Invalid value {{value}} for vector {{vector}}" . ($this->index !== null ? " at index {{index}}" : "") . 
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
		$this->addProperty('index')->setAsStrictInteger(true, null, true)->setDefaultValue(null);
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
