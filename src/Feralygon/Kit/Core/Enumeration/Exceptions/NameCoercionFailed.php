<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Enumeration\Exceptions;

use Feralygon\Kit\Core\Enumeration\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;

/**
 * Core enumeration name coercion failed exception class.
 * 
 * This exception is thrown from an enumeration whenever the coercion into an enumerated element name has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 */
class NameCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Name coercion failed with value {{value}} in enumeration {{enumeration}}.\n" . 
			"HINT: Only enumerated elements can be evaluated into enumerated element names.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['value']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
		}
		return parent::evaluateProperty($name, $value);
	}
}
