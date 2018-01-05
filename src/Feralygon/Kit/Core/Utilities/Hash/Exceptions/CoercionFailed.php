<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Hash\Exceptions;

use Feralygon\Kit\Core\Utilities\Hash\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core hash utility coercion failed exception class.
 * 
 * This exception is thrown from the hash utility whenever the coercion has failed with a given value for a given number of bits.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read int $bits <p>The number of bits.</p>
 */
class CoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return $this->get('bits') === 1
			? "Coercion failed with value {{value}} for {{bits}} bit."
			: "Coercion failed with value {{value}} for {{bits}} bits.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['value', 'bits'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'value':
				return true;
			case 'bits':
				return UType::evaluateInteger($value);
		}
		return null;
	}
}
