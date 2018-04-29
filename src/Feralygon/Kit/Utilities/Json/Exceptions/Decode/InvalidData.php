<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Utilities\Json\Exceptions\Decode;

use Feralygon\Kit\Utilities\Json\Exceptions\Decode as Exception;

/**
 * This exception is thrown from the JSON utility <code>decode</code> method whenever given data is invalid.
 * 
 * @since 1.0.0
 * @property-read string $data
 * <p>The data.</p>
 * @property-read int|null $error_code [default = null]
 * <p>The error code.</p>
 * @property-read string|null $error_message [default = null]
 * <p>The error message.</p>
 */
class InvalidData extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Invalid data {{data}}.";
		if ($this->isset('error_message')) {
			$message .= "\nERROR: {{error_message}}";
		}
		return $message;
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function loadProperties() : void
	{
		$this->addProperty('data')->setAsStrictString()->setAsRequired();
		$this->addProperty('error_code')->setAsStrictInteger(false, null, true)->setDefaultValue(null);
		$this->addProperty('error_message')->setAsString(false, true)->setDefaultValue(null);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'error_message' && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
