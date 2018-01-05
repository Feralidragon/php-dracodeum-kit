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
 * Core component invalid prototype base class exception class.
 * 
 * This exception is thrown from a component whenever a given prototype base class is invalid.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Component $component <p>The component instance.</p>
 * @property-read string $base_class <p>The base class.</p>
 */
class InvalidPrototypeBaseClass extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid prototype base class {{base_class}} in component {{component}}.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['component', 'base_class'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'component':
				return is_object($value) && UType::isA($value, Component::class);
			case 'base_class':
				return UType::evaluateString($value);
		}
		return null;
	}
}
