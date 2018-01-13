<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Data\Exceptions;

use Feralygon\Kit\Core\Utilities\Data\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core data utility coercion failed exception class.
 * 
 * This exception is thrown from the data utility whenever the coercion has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read string|null $hint_message [default = null] <p>The hint message.</p>
 */
class CoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Coercion failed with value {{value}}.";
		if ($this->isset('hint_message')) {
			$message .= "\nHINT: {{hint_message}}";
		}
		return $message;
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
			case 'hint_message':
				return UType::evaluateString($value, true);
		}
		return null;
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function getPlaceholderValueString(string $placeholder, $value) : string
	{
		if ($placeholder === 'hint_message') {
			return $value;
		}
		return parent::getPlaceholderValueString($placeholder, $value);
	}
}
