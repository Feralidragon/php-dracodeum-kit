<?php

/**
 * @author Cláudio "Feralidragon" Luís <claudio.luis@aptoide.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Feralygon\Kit\Core\Component\Exceptions;

use Feralygon\Kit\Core\Component\Exception;
use Feralygon\Kit\Core\Utilities\Type as UType;

/**
 * Core component prototype name not found exception class.
 * 
 * This exception is thrown from a component whenever a given prototype name is not found.
 * 
 * @since 1.0.0
 * @property-read string $name <p>The prototype name.</p>
 */
class PrototypeNameNotFound extends Exception
{
	//Implemented public methods
	/** {@inheritdoc} */
	public function getDefaultMessage() : string
	{
		return "Prototype name {{name}} not found for component {{component}}.";
	}
	
	
	
	//Overridden public static methods
	/** {@inheritdoc} */
	public static function getRequiredPropertyNames() : array
	{
		return array_merge(parent::getRequiredPropertyNames(), ['name']);
	}
	
	
	
	//Overridden protected methods
	/** {@inheritdoc} */
	protected function evaluateProperty(string $name, &$value) : ?bool
	{
		switch ($name) {
			case 'name':
				return UType::evaluateString($value);
		}
		return parent::evaluateProperty($name, $value);
	}
}
