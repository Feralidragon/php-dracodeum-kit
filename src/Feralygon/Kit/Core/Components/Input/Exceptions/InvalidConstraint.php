<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Components\Input\Exceptions;

use Feralygon\Kit\Core\Components\Input;
use Feralygon\Kit\Core\Components\Input\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core input component invalid constraint exception class.
 * 
 * This exception is thrown from an input whenever a given constraint is invalid.
 * 
 * @since 1.0.0
 * @property-read mixed $constraint <p>The constraint.</p>
 * @property-read \Feralygon\Kit\Core\Components\Input $component <p>The input component instance.</p>
 * @property-read \Feralygon\Kit\Core\Prototypes\Input $prototype <p>The input prototype instance.</p>
 */
class InvalidConstraint extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Invalid constraint {{constraint}} in input {{component}} (with prototype {{prototype}}).\n" . 
			"HINT: Only a constraint component instance, prototype instance, class or name is allowed.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['constraint', 'component', 'prototype'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'constraint':
				return true;
			case 'component':
				return is_object($value) && UType::isA($value, Input::class);
			case 'prototype':
				return is_object($value) && UType::isA($value, Input::getPrototypeBaseClass());
		}
		return null;
	}
}
