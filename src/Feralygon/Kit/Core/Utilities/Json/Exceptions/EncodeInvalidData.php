<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Json\Exceptions;

use Feralygon\Kit\Core\Utilities\{
	Text as UText,
	Type as UType
};

/**
 * Core JSON utility encode method invalid data exception class.
 * 
 * This exception is thrown from the JSON utility encode method whenever given data is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $data <p>The data.</p>
 * @property-read int|null $error_code [default = null] <p>The error code.</p>
 * @property-read string|null $error_message [default = null] <p>The error message.</p>
 */
class EncodeInvalidData extends Encode
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
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['data'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'data':
				return true;
			case 'error_code':
				return UType::evaluateInteger($value, true);
			case 'error_message':
				return UType::evaluateString($value, true);
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'data') {
			return UText::stringify($value, null, ['prepend_type' => true]);
		} elseif ($placeholder === 'error_message' && is_string($value)) {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
