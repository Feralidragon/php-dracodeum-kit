<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration;
use Feralygon\Kit\Core\Enumeration\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core enumeration value coercion failed exception class.
 * 
 * This exception is thrown from an enumeration whenever the coercion into an enumerated element value has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read string $enumeration <p>The enumeration class.</p>
 * @property-read mixed $value <p>The value.</p>
 */
class ValueCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Value coercion failed with value {{value}} in enumeration {{enumeration}}.\n" . 
			"HINT: Only enumerated elements can be evaluated into enumerated element values.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['enumeration', 'value'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'enumeration':
				return UType::evaluateClass($value, Enumeration::class);
			case 'value':
				return true;
		}
		return null;
	}
}
