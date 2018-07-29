<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Primitives\Dictionary\Exceptions;

use Feralygon\Kit\Primitives\Dictionary\Exception;
use Feralygon\Kit\Utilities\Text as UText;

/**
 * This exception is thrown from a dictionary whenever a given key is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $key
 * <p>The key.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 */
class InvalidKey extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage(): string
	{
		return $this->isset('error_message')
			? "Invalid key {{key}} for dictionary {{dictionary}}, with the following error: {{error_message}}"
			: "Invalid key {{key}} for dictionary {{dictionary}}.";
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function loadProperties(): void
	{
		//parent
		parent::loadProperties();
		
		//properties
		$this->addProperty('key');
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
