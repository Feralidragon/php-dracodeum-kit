<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Utilities\Type\Exceptions;

use Feralygon\Kit\Core\Utilities\Type\Exception;
use Feralygon\Kit\Core\Interfaces\Throwables\Coercion as ICoercion;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core type utility object coercion failed exception class.
 * 
 * This exception is thrown from the type utility whenever the coercion into an object has failed with a given value.
 * 
 * @since 1.0.0
 * @property-read mixed $value <p>The value.</p>
 * @property-read object|string|null $base_object_class [default = null] <p>The base object or class.</p>
 */
class ObjectCoercionFailed extends Exception implements ICoercion
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Object coercion failed with value {{value}}.\n" . 
			($this->isset('base_object_class')
				? "HINT: Only class strings and objects can be coerced into objects, and they must be or extend from {{base_object_class}}."
				: "HINT: Only class strings and objects can be coerced into objects."
			);
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
			case 'base_object_class':
				return UType::evaluateObjectClass($value, null, true);
		}
		return null;
	}
}
