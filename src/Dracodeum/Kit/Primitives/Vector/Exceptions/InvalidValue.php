<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Dracodeum\Kit\Primitives\Vector\Exceptions;

use Dracodeum\Kit\Primitives\Vector\Exception;
use Dracodeum\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from a vector whenever a given value is invalid.
 * 
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read int|null $index [strict] [default = null]
 * <p>The index.<br>
 * If set, then it must be greater than or equal to <code>0</code>.</p>
 * @property-read string|null $error_message [coercive] [default = null]
 * <p>The error message.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		if ($this->error_message !== null) {
			return $this->index !== null
				? "Invalid value {{value}} for vector {{vector}} at index {{index}}, " . 
					"with the following error: {{error_message}}"
				: "Invalid value {{value}} for vector {{vector}}, with the following error: {{error_message}}";
		}
		return $this->index !== null
			? "Invalid value {{value}} for vector {{vector}} at index {{index}}."
			: "Invalid value {{value}} for vector {{vector}}.";
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
