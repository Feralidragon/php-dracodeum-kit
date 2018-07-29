<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Dictionary\Exceptions;

use Feralygon\Kit\Primitives\Dictionary\Exception;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from a dictionary whenever a given value is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $value
 * <p>The value.</p>
 * @property-read bool $has_key [default = false]
 * <p>Indicate that a key has been given.</p>
 * @property-read mixed $key [default = null]
 * <p>The key.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 */
class InvalidValue extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		if ($this->isset('error_message')) {
			return $this->is('has_key')
				? "Invalid value {{value}} for dictionary {{dictionary}} at key {{key}}, " . 
					"with the following error: {{error_message}}"
				: "Invalid value {{value}} for dictionary {{dictionary}}, with the following error: {{error_message}}";
		}
		return $this->is('has_key')
			? "Invalid value {{value}} for dictionary {{dictionary}} at key {{key}}."
			: "Invalid value {{value}} for dictionary {{dictionary}}.";
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
			return UText::uncapitalize($value, true);
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
