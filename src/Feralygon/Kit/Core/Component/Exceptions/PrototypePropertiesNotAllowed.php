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
 * Core component prototype properties not allowed exception class.
 * 
 * This exception is thrown from a component whenever prototype properties are given although they are not allowed.
 * 
 * @since 1.0.0
 * @property-read \Feralygon\Kit\Core\Component $component <p>The component instance.</p>
 */
class PrototypePropertiesNotAllowed extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Prototype properties not allowed in component {{component}}.\n" . 
			"HINT: Prototype properties are only allowed whenever the prototype is given as a class or not given at all.";
	}
	
	
	
	//Implemented public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return ['component'];
	}
	
	
	
	//Implemented protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'component':
				return is_object($value) && UType::isA($value, Component::class);
		}
		return null;
	}
}
