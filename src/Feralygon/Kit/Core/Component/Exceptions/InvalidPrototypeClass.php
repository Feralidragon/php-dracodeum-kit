<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Exceptions;

use Feralygon\Kit\Core\Component;
use Feralygon\Kit\Core\Component\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core component invalid prototype class exception class.
 * 
 * This exception is thrown from a component whenever a given prototype class is invalid.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Component $component <p>The component instance.</p>
 * @property-read string $class <p>The class.</p>
 * @property-read string $base_class <p>The base class.</p>
 */
class InvalidPrototypeClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype class {{class}} in component {{component}}.\n" . 
			"HINT: Only a class or subclass of {{base_class}} is allowed for this component.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['component', 'class', 'base_class'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'component':
				return is_object($value) && UType::isA($value, Component::class);
			case 'class':
				//no break
			case 'base_class':
				return UType::evaluateString($value);
		}
		return null;
	}
}
