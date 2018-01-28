<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Traits\NonInstantiable\Exceptions;

use Feralygon\Kit\Core\Traits\NonInstantiable\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core non-instantiable trait cannot instantiate exception class.
 * 
 * This exception is thrown from a class using the non-instantiable trait whenever the instantiation 
 * of a class is attempted.
 * 
 * @since 1.0.0
 * @property-read string $class <p>The class.</p>
 */
class CannotInstantiate extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Cannot instantiate class {{class}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['class'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'class':
				return UType::evaluateClass($value);
		}
		return null;
	}
}
