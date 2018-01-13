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
 * @property-read bool $non_associative [default = false] <p>Non-associative array restriction.</p>
 * @property-read bool $non_empty [default = false] <p>Non-empty array restriction.</p>
 */
class CoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		$message = "Coercion failed with value {{value}}.";
		if ($this->is('non_associative') && $this->is('non_empty')) {
			$message .= "\nHINT: Only a non-associative and non-empty array is allowed.";
		} elseif ($this->is('non_associative')) {
			$message .= "\nHINT: Only a non-associative array is allowed.";
		} elseif ($this->is('non_empty')) {
			$message .= "\nHINT: Only a non-empty array is allowed.";
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
			case 'non_associative':
				//no break
			case 'non_empty':
				$value = $value ?? false;
				return UType::evaluateBoolean($value);
		}
		return null;
	}
}
